<?php

namespace Jakmall\Recruitment\Calculator\History;

use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class CommandHistoryManager implements CommandHistoryManagerInterface
{
    /**
     * Returns array of command history.
     *
     * @return array returns an array of commands in storage
     */
    public function findAll($drivers, $id = array()): array 
    {
        $result = array();

        if (!file_exists('storage/'.$this->driver($drivers)['file'].'.log')) {
            return $result;
        }

        $file = file_get_contents('storage/'.$this->driver($drivers)['file'].'.log');
        $json = explode(';',substr($file, 0, -2));
        
        foreach ($json as $value) {
            if (!empty($id)) {
                if (json_decode($value, true)['id'] == $id[0]) {
                    array_push($result,json_decode($value, true));
                }
            } else {
                array_push($result,json_decode($value, true));
            }
        }

        return $result;
    }

    /**
     * Filter a command by id.
     *
     * @param string|int $id
     *
     * @return null|mixed returns null when id not found.
     */
    public function filter($data, $id)
    {
        for ($i=0; $i < count($data); $i++) { 
            if ($data[$i]['id'] == $id) {
                unset($data[$i]);
            }
        }

        return $data;
    }

    /**
     * Log command data to storage.
     *
     * @param mixed $command The command to log.
     *
     * @return bool Returns true when command is logged successfully, false otherwise.
     */
    public function log($command): bool
    {
        $lastId = end($this->findAll('mesinhitung'));
        $id = $lastId['id'] + 1 ?? 0;
        $json = json_encode(
                array(
                    'id' => $id,
                    'command' => $command['command'],
                    'operation' => $command['description'],
                    'result' => $command['result'],
                )
            );
        $this->saveToStorage($json, 'mesinhitung');
        $this->saveToStorage($json, 'latest');

        $data = $this->findAll('latest');
        
        if (count($data) > 10) {
            unset($data[0]);
            $this->clearAll('latest.log', 'clean');
            foreach ($data as $value) {
                $this->saveToStorage(json_encode($value), 'latest');
            }
        }

        return true;
    }

    /**
     * Clear a command by id
     *
     * @param string|int $id
     *
     * @return bool Returns true when data with $id is cleared successfully, false otherwise.
     */
    public function clear($driver, $id): bool
    {
        $data = $this->findAll($this->driver($driver)['name']);
        $data = $this->filter($data, $id);
        $this->clearAll($this->driver($driver)['file'].'.log', 'clean');

        if ($driver == 'file') {
            $dataArray = array_slice($data, -10);
            $this->clearAll('latest.log', 'clean');

            foreach ($dataArray as $value) {
                $this->saveToStorage(json_encode($value), 'latest');
            }
        }

        foreach ($data as $value) {
            $this->saveToStorage(json_encode($value), $this->driver($driver)['file']);
        }
        
        return true;
    }

    /**
     * Clear all data from storage.
     *
     * @return bool Returns true if all data is cleared successfully, false otherwise.
     */
    public function clearAll($driver, $context): bool
    {
        if ($context == 'clean') {
            file_put_contents('storage/'.$driver, "");
        }

        if (file_exists('storage/'.$driver) && $context = 'delete') {
            unlink('storage/'.$driver);
        }

        return true;
    }

    /**
     * Save data to storage.
     *
     * @return bool Returns true if all data is cleared successfully, false otherwise.
     */
    public function saveToStorage($log_msg, $name) : void
    {
        $log_filename = $name;

        if (!is_dir('storage')) {
            mkdir('storage', 0777, true);
        }

        $log_file_data = $log_filename.'.log';
        file_put_contents('storage/'.$log_file_data, $log_msg . ";\n", FILE_APPEND);
    }

    public function driver($driver) : array
    {
        switch ($driver) {
            case "file":
                $driver = array('file' => "mesinhitung", 'name' => "file");
                break;
            case "latest":
                $driver = array('file' => "latest", 'name' => "latest");
                break;
            default:
                $driver = array('file' => "mesinhitung", 'name' => "file");
        }
        
        return $driver;
    }

    /**
     * Find a command by id.
     *
     * @param string|int $id
     *
     * @return null|mixed returns null when id not found.
     */
    public function find($id)
    {
        $result = array();
        $file = file_get_contents('storage/mesinhitung.log');
        $json = explode(';', substr($file, 0, -2));

        foreach ($json as $value) {

            if (json_decode($value, true)['id'] == $id) {

                array_push($result, json_decode($value, true));
            }
        }

        return $result[0];
    }
}
