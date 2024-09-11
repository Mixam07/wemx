<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class FileManager extends Pterodactyl
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
     * @param string $path
     * @return mixed
     */
    public function listFiles(string $uuidShort, string $path = '/'): mixed
    {
        return $this->ptero->makeRequest('get', $this->endpoint . '/' . $uuidShort . '/files/list', ['directory' => $path]);
    }

    /**
     * @param string $uuidShort
     * @param string $filePath
     * @return mixed
     */
    public function getFileContents(string $uuidShort, string $filePath): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/contents?file=%2F' . $filePath;
        $resp = $this->ptero->makeRequest('get', $url);
        return is_array($resp) ? $resp : $resp->body();

    }

    /**
     * @param string $uuidShort
     * @param string $filePath
     * @return mixed
     */
    public function downloadFile(string $uuidShort, string $filePath): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/download';
        return $this->ptero->makeRequest('get', $url, ['file' => $filePath]);
    }

    /**
     * @param string $uuidShort
     * @param string $oldName
     * @param string $newName
     * @param string $path
     * @return mixed
     */
    public function renameFile(string $uuidShort, string $oldName, string $newName, string $path = '/'): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/rename';
        $data['root'] = $path;
        $data['files'] = [['from' => $oldName, 'to' => $newName]];
        return $this->ptero->makeRequest('put', $url, $data);
    }

    /**
     * @param string $uuidShort
     * @param string $filePath
     * @return mixed
     */
    public function copyFile(string $uuidShort, string $filePath): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/copy';
        return $this->ptero->makeRequest('post', $url, ['location' => $filePath]);
    }

    /**
     * @param string $uuidShort
     * @param string $filePath
     * @param string $content
     * @return mixed
     */
    public function writeFile(string $uuidShort, string $filePath, string $content): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/write?file=' . $filePath;
        return $this->ptero->makeRequest('post', $url, $content);
    }

    /**
     * @param string $uuidShort
     * @param array $files
     * @param string $filePath
     * @return mixed
     */
    public function compressFiles(string $uuidShort, array $files, string $filePath = '/'): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/compress';
        $data = ['root' => $filePath, 'files' => $files];
        return $this->ptero->makeRequest('post', $url, $data);
    }

    /**
     * @param string $uuidShort
     * @param string $fileName
     * @param string $filePath
     * @return mixed
     */
    public function decompressFile(string $uuidShort, string $fileName, string $filePath = '/'): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/decompress';
        $data = ['root' => $filePath, 'file' => $fileName];
        return $this->ptero->makeRequest('post', $url, $data);
    }

    /**
     * @param string $uuidShort
     * @param array $files
     * @param string $filePath
     * @return mixed
     */
    public function deleteFiles(string $uuidShort, array $files, string $filePath): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/delete';
        $data = ['root' => $filePath, 'files' => $files];
        return $this->ptero->makeRequest('post', $url, $data);
    }

    /**
     * @param string $uuidShort
     * @param string $folderName
     * @param string $path
     * @return mixed
     */
    public function createFolder(string $uuidShort, string $folderName, string $path = '/'): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/create-folder';
        $data = ['root' => $path, 'name' => $folderName];
        return $this->ptero->makeRequest('post', $url, $data);
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function getUploadUrl(string $uuidShort): mixed
    {
        $url = $this->endpoint . '/' . $uuidShort . '/files/upload';
        return $this->ptero->makeRequest('get', $url);
    }
}
