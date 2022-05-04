<?php

namespace App;

use App\Exception\IOException;
use SplFileInfo;

interface IFileService
{

    /**
     * Reads all content from file, not for big files
     *
     * @param string $path Path to file
     * @param string $mode File mode to use (w,w+,r...)
     * @return SplFileInfo File
     */
    public function readFile(string $path, string $mode): SplFileInfo;

    /**
     * Writes content to file
     *
     * @param string $path Path to file
     * @param string $content Content to write
     * @param string $mode File mode to use (w,w+,r...)
     * @return SplFileInfo File
     * @throws IOException
     */
    public function writeFile(string $path, string $content, string $mode): SplFileInfo;

    /**
     * Checks if file exists
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Lists all files in specific folder
     *
     * @param string $folder Folder to list
     * @param string $patter Search files by pattern
     * @return array<SplFileInfo>
     */
    public function list(string $folder, string $patter = ""): array;

}