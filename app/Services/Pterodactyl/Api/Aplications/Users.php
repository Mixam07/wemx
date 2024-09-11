<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Users extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/users';
    }

    /**
     * @param string|null $filters
     * @return mixed
     */
    public function all(string $filters = NULL): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . $filters);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $id);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getExternal(string $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/external/' . $id);
    }

    /**
     * @param array $params
     * @return mixed
     * $params = [
     * "email" => "example10@gmail.com",
     * "username" => "exampleuser",
     * "first_name" => "Example",
     * "last_name" => "User"
     * ]
     */
    public function create(array $params): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint, $params);
    }

    /**
     * @param int $user_id
     * @param array $params
     * @return mixed
     * $params = [
     * "email" => "example10@gmail.com",
     * "username" => "exampleuser",
     * "first_name" => "Example",
     * "last_name" => "User"
     * ]
     */
    public function update(int $user_id, array $params): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $user_id, $params);
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public function delete(int $user_id): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $user_id);
    }
}
