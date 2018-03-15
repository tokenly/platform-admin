<?php

namespace Tokenly\PlatformAdmin\Jobs\Output;

use Illuminate\Support\Carbon;
use Pusher\Pusher;
use Symfony\Component\Console\Output\Output;
use Tokenly\LaravelEventLog\Facade\EventLog;

class PusherChannelOutput extends Output
{
    private $buffer = '';
    private $next_message = '';
    private $exit_status = null;

    protected $last_flush_time;
    protected $last_msg_time;

    protected $pusher_connection;
    protected $channel_name;

    const SMALL_BUFFER_SIZE = 256;
    const FLUSH_DELAY = 15; // every 15 seconds, flush the entire output (not just the last entry)
    const MIN_MESSAGE_DELAY_MS = 350; // minimum delay in milliseconds

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        $content = $this->buffer;
        $this->buffer = '';

        return $content;
    }

    public function setPusherConnectionAndChannel(Pusher $pusher_connection, $channel_name)
    {
        $this->pusher_connection = $pusher_connection;
        $this->channel_name = $channel_name;
    }

    public function open()
    {
        $this->last_flush_time = Carbon::now();
        $this->last_msg_time = 0;
    }

    public function close($exit_status)
    {
        $this->exit_status = $exit_status;
        $this->flushAllToPusher(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        if ($newline) {
            $message .= PHP_EOL;
        }

        $this->buffer .= $message;
        $this->next_message .= $message;

        if (Carbon::now()->diffInSeconds($this->last_flush_time) > self::FLUSH_DELAY) {
            $this->flushAllToPusher(true);
        } else {
            if (strlen($this->buffer) < self::SMALL_BUFFER_SIZE) {
                $this->flushAllToPusher(false);
            } else {
                $this->flushDeltaToPusher($this->next_message);
            }
        }
    }

    protected function flushAllToPusher($force = false)
    {
        $flushed = $this->flushMessageToPusher($this->buffer, true, $force);
        if ($flushed) {
            $this->last_flush_time = Carbon::now();
        }
    }

    protected function flushDeltaToPusher($message)
    {

        $this->flushMessageToPusher($message, false);
    }

    protected function flushMessageToPusher($string, $is_full, $force = false)
    {
        if (!$this->pusher_connection) {
            EventLog::logError('pusher.undefined', ['string' => $string, 'isFull', '$is_full']);
            return false;
        }

        $ts = intval(microtime(true) * 1000);

        // if we just sent a message, then delay
        if (!$force and ($ts - $this->last_msg_time < self::MIN_MESSAGE_DELAY_MS)) {
            return false;
        }

        // split into 9k chunks
        $string_chunks = str_split($string, 9000);
        foreach($string_chunks as $offset => $string_chunk) {
            $is_first = ($offset === 0);
            $data = [
                'msg' => $string_chunk,
                'full' => !!$is_full and $is_first,
                'exitStatus' => $this->exit_status,
                'ts' => $ts,
            ];

            $result = $this->pusher_connection->trigger($this->channel_name, 'commandOutput', $data);
        }


        $this->last_msg_time = intval(microtime(true) * 1000);
        $this->next_message = '';
        return true;
    }
}
