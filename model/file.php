<?php

class file
{

    public $file_name_list;
    private $dir_name = '1out';
    private $file_path;

    public function __construct($path)
    {
        if (empty($path)) {
            throw new Exception('empty $path');
        }
        $this->file_path = "{$path}/{$this->dir_name}";
        if (!is_dir($this->file_path)) {
            throw new Exception('empty file path');
        }
        $this->file_name_list = $this->get_file_name_list();
    }

    private function get_file_name_list()
    {
        $dir = opendir($this->file_path);
        while ($file_name = readdir($dir)) {
            if ($file_name != '.' && $file_name != '..') {
                list($name, $extension) = explode('.', $file_name);
                $file_name_list[] = $name;
            }
        }
        return array_unique($file_name_list);
    }

    public function get_file($file_name)
    {
        return file_get_contents("{$this->file_path}/{$file_name}");
    }

}
