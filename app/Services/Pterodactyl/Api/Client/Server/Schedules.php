<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Schedules extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = 'api/client/servers';
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function all(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/schedules');
    }

    /**
     * @param string $uuidShort
     * @param string $scheduleId
     * @return mixed
     */
    public function get(string $uuidShort, string $scheduleId): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId);
    }

    /**
     * @param string $uuidShort
     * @param string $name
     * @param string $minute
     * @param string $hour
     * @param string $month
     * @param string $day_of_week
     * @param string $day_of_month
     * @param bool $is_active
     * @param bool $only_when_online
     * @return mixed
     */
    public function create(string $uuidShort,string $name, string $minute, string $hour, string $month, string $day_of_week, string $day_of_month, bool $is_active = true, bool $only_when_online = true): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/schedules',
            [
                'name' => $name,
                'minute' => $minute,
                'hour' => $hour,
                'month' => $month,
                'day_of_week' => $day_of_week,
                'day_of_month' => $day_of_month,
                'is_active' => $is_active,
                'only_when_online' => $only_when_online
            ]
        );
    }

    /**
     * @param string $uuidShort
     * @param int $scheduleId
     * @param string $name
     * @param string $minute
     * @param string $hour
     * @param string $month
     * @param string $day_of_week
     * @param string $day_of_month
     * @param bool $is_active
     * @param bool $only_when_online
     * @return mixed
     */
    public function update(string $uuidShort, int $scheduleId, string $name, string $minute, string $hour, string $month, string $day_of_week, string $day_of_month, bool $is_active = true, bool $only_when_online = true): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId,
            [
                'name' => $name,
                'minute' => $minute,
                'hour' => $hour,
                'month' => $month,
                'day_of_week' => $day_of_week,
                'day_of_month' => $day_of_month,
                'is_active' => $is_active,
                'only_when_online' => $only_when_online
            ]
        );
    }

    /**
     * @param string $uuidShort
     * @param int $scheduleId
     * @return mixed
     */
    public function execute(string $uuidShort, int $scheduleId): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId . '/execute');
    }


    /**
     * Summary of delete
     * @param string $uuidShort
     * @param string $scheduleId
     * @return mixed
     */
    public function delete(string $uuidShort, string $scheduleId): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId);
    }

    /**
     * @param string $uuidShort
     * @param string $scheduleId
     * @param array $task ['action' => 'command|power|backup', 'payload' => 'string', 'time_offset' => 'int', 'continue_on_failure' => 'bool']
     * ['power => ['payload' => 'start|stop|restart|kill']
     * ['command => ['payload' => 'string command']
     * ['backup => ['payload' => 'Ignored Files']
     * @return mixed
     */
    public function createTask(string $uuidShort, string $scheduleId, array $task): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks', $task);
    }

    /**
     * @param string $uuidShort
     * @param string $scheduleId
     * @param string $taskId
     * @param array $task ['action' => 'command|power|backup', 'payload' => 'string', 'time_offset' => 'int', 'continue_on_failure' => 'bool']
     * ['power => ['payload' => 'start|stop|restart|kill']
     * ['command => ['payload' => 'string command']
     * ['backup => ['payload' => 'Ignored Files']
     * @return mixed
     */
    public function updateTask(string $uuidShort, string $scheduleId, string $taskId, array $task): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId, $task);
    }

    /**
     * @param string $uuidShort
     * @param string $scheduleId
     * @param string $taskId
     * @return mixed
     */
    public function deleteTask(string $uuidShort, string $scheduleId, string $taskId): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId);
    }


}
