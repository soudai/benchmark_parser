<?php

class parse
{

    private $file;
    private $method;
    private $vmstat_summary;
    private $out_summary;
    private $type;
    private $type_list = [
        '-select-',
        '-delete-',
        '-update-',
        '-insert-',
        '-selectP-',
        '-deleteP-',
        '-updateP-',
        '-insertP-',
    ];

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function get()
    {
        foreach ($this->file->file_name_list as $file_name) {
            if (!$this->set($file_name)) {
                continue;
            }
            $parse_data["$file_name"] = $this->parse($file_name);
        }
        return $parse_data;
    }

    private function parse($file_name)
    {
        $vmstat_file_data = $this->file->get_file("{$file_name}.vmstat");
        $vmstat_parse_data = self::vmstat($vmstat_file_data);

        $out_file_data = $this->file->get_file("{$file_name}.out");

        $out_parse_data = $this->out($out_file_data);

        return[
            'vmstat_parse_data' => $vmstat_parse_data,
            'out_parse_data' => $out_parse_data,
        ];
    }

    private function set($file_name)
    {
        $method = function ($list) use ($file_name) {
            if (mb_strpos($file_name, $list) === false) {
                return;
            }
            $this->type = $list;
            return;
        };
        unset($this->type);
        array_map($method, $this->type_list);
        if (empty($this->type)) {
            return false;
        }
        return true;
    }

    private static function vmstat($file_data)
    {
        $records = array_filter(explode("\n", $file_data));
        //先頭行を削除
        array_shift($records);
        $filter = function ($target) {
            if (is_null($target)) {
                return false;
            }
            if ($target === '') {
                return false;
            }
            return true;
        };
        $header = array_filter(explode(" ", array_shift($records)), $filter);
        foreach ($records as $record) {
            $tmp_record = array_filter(explode(" ", $record), $filter);
            $vmstat[] = array_combine($header, $tmp_record);
        }
        return self::vmstat_average($vmstat, $header);
    }

    private static function vmstat_average($vmstat_parse_data, $header)
    {
        foreach ($header as $column) {
            $tmp = array_column($vmstat_parse_data, $column);
            $vmstat_summary["$column"] = array_sum($tmp) / count($tmp);
        }
        return $vmstat_summary;
    }

    private function out($file_data)
    {
        $records = array_filter(explode("\n", $file_data));
        //先頭行を削除
        array_shift($records);
        $filter = function ($target) {
            if (is_null($target)) {
                return false;
            }
            if ($target === '') {
                return false;
            }
            return true;
        };

        foreach ($records as $record) {
            if (mb_strpos($record, '=') !== false) {
                list($name, $value) = explode('=', $record);
                $out["$name"] = $value;
                continue;
            }
            if (mb_strpos($record, 'nclient:') !== false) {
                $header = array_filter(explode(" ", $record), $filter);
                continue;
            }
            if (isset($header)) {
                $tmp = array_filter(explode("\t", $record), $filter);
                foreach ($header as $key => $value) {
                    $tmp_header[] = mb_convert_encoding(trim(trim($value, "\t")), 'UTF-8', 'EUC-JP');
                }
                $out += array_combine($tmp_header, $tmp);
                unset($header);
                unset($tmp_header);
                continue;
            }
        }
        return $out;
    }

}
