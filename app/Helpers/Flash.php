<?php namespace App\Helpers;

use Session;

/**
 * Flash Message Helper.
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class Flash {

    const SUCCESS = 'success';
    const INFO    = 'info';
    const WARNING = 'warning';
    const ERROR   = 'error';

    private static $sessionKey = 'cool_flash_message';

    /**
     * Sets a success message.
     *
     * @param  string  $message
     * @return void
     */
    public static function success($message)
    {
        self::set(self::SUCCESS, $message);
    }

    /**
     * Sets an info message.
     *
     * @param  string  $message
     * @return void
     */
    public static function info($message)
    {
        self::set(self::INFO, $message);
    }

    /**
     * Sets a warning message.
     *
     * @param  string  $message
     * @return void
     */
    public static function warning($message)
    {
        self::set(self::WARNING, $message);
    }

    /**
     * Sets an error message.
     *
     * @param  string|array|\Illuminate\Validation\Validator|\Illuminate\Support\MessageBag  $message
     * @return void
     */
    public static function error($message)
    {
        if ($message instanceof \Illuminate\Validation\Validator)
        {
            $message = $message->messages()->all(':message');
        }
        elseif ($message instanceof \Illuminate\Support\MessageBag)
        {
            $message = $message->all(':message');
        }

        self::set(self::ERROR, $message);
    }

    /**
     * Gets the plain message.
     *
     * @return array|NULL
     */
    public static function get()
    {
        return Session::pull(self::$sessionKey, NULL);
    }

    /**
     * Renders the message as a an HTML view.
     *
     * @return string|NULL
     */
    public static function getAsHTML()
    {
        $value = self::get();

        if ($value === NULL)
        {
            return NULL;
        }

        return self::view($value['message'], $value['level']);
    }

    /**
     * Makes the HTML view for a message.
     *
     * @param  string  $level  success|info|warning|error
     * @param  string|array|\App\Exceptions\ValidationException|\Illuminate\Validation\Validator|\Illuminate\Support\MessageBag  $message
     * @return string
     */
    public static function view($message, $level = 'error')
    {
        switch ($level)
        {
            case self::SUCCESS:
            case self::INFO:
            case self::WARNING:

                if ( ! is_string($message))
                {
                    $message = NULL;
                }

                break;

            case self::ERROR:

                if (is_string($message) || is_array($message))
                {
                    // Do Nothing
                }
                elseif ($message instanceof \Illuminate\Validation\Validator)
                {
                    $message = $message->messages()->all(':message');
                }
                elseif ($message instanceof \Illuminate\Support\MessageBag)
                {
                    $message = $message->all(':message');
                }
                elseif ($message instanceof \App\Exceptions\ValidationException)
                {
                    $message = $message->errors()->all(':message');
                }
                else
                {
                    $message = NULL;
                }

                break;

            default:

                return NULL;
        }

        return view('flash_messages.' . $level, ['message' => $message])
            ->render();
    }

    /**
     * Sets a message.
     *
     * @param   string        $level    success|info|warning|error
     * @param   string|array  $message
     * @return  string
     */
    private static function set($level, $message)
    {
        Session::flash(self::$sessionKey, ['level' => $level, 'message' => $message]);
    }
}
