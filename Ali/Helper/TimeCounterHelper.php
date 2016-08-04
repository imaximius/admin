<?php

namespace Araneum\AdminBundle\Ali\Helper;

/**
 * Class TimeCounterHelper
 *
 * @package Araneum\AdminBundle\Ali\Helper
 */
class TimeCounterHelper
{
    const TIME_IN_HOUR   = 3600;
    const TIME_IN_MINUTE = 60;

    /**
     * Convert int to time
     *
     * @param  int $time
     * @return string
     */
    public static function intToString($time)
    {
        $seconds = $time % 60;
        $secondsValue = ($seconds < 10) ? '0'.$seconds : $seconds;
        $time = ($time - $seconds) / 60;
        $minutes = $time % 60;
        $minutesValue = ($minutes < 10) ? '0'.$minutes : $minutes;
        $hours = ($time - $minutes) / 60;
        $hoursValue = ($hours < 10) ? '0'.$hours : $hours;

        return $hoursValue.":".$minutesValue.":".$secondsValue;
    }

    /**
     * String to int
     *
     * @param  string $timeString
     * @return mixed
     */
    public function stringToInt($timeString)
    {
        $timeArray = explode(':', $timeString);
        if (count($timeArray) == 3) {
            $result = $timeArray[0] * self::TIME_IN_HOUR + $timeArray[1] * self::TIME_IN_MINUTE + $timeArray[2];

            return $result;
        }
    }

    /**
     * Validate time online
     *
     * @param  int $time
     * @return bool
     */
    public function validateTimeCounter($time)
    {
        $exp = '#^(([0-9]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$#';
        if (preg_match($exp, $time)) {
            return true;
        } else {
            return false;
        }
    }
}
