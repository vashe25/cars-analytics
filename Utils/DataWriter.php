<?php

namespace Utils;
use Env;

class DataWriter
{
    public function __construct(private string $baseFolder = 'data')
    {
        $this->checkDir();
    }

    public function write(string $filename, string $data): void
    {
        file_put_contents($this->makePath($filename), $data);
    }

    public function read(string $filename): string
    {
        return file_get_contents($this->makePath($filename));
    }

    private function makePath(string $filename): string
    {
        return sprintf('%s/%s/%s', Env::projectDir, $this->baseFolder, $filename);
    }

    private function checkDir(): void
    {
        $dirPath = sprintf('%s/%s', Env::projectDir, $this->baseFolder);
        if (!is_dir($dirPath)) {
            mkdir($dirPath, recursive: true);
        }
    }
}