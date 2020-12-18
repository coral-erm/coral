<?php


if (!function_exists('safe_file_rewrite')) {
    /**
     * Safely rewrites a file
     *
     * @param string $fileName A path to a file
     *
     * @param string $dataToSave the string to rewrite the file
     */
    function safe_file_rewrite($fileName, $dataToSave)
    {
        if (!is_writable($fileName)) {
            throw new Exception("$fileName is not writeable.");
        }

        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime(TRUE);

            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite) {
                    usleep(round(rand(0, 100) * 1000));
                }
            } while ((!$canWrite) and ((microtime(TRUE) - $startTime) < 5));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

}