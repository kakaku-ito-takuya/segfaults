<?php
    ini_set('memory_limit', '1024M');
    $fileObject = new SplFileObject('./dictionary.csv', 'r');
    $data = [];
    while (!$fileObject->eof()) {
        $row = $fileObject->fgetcsv();
        $data[] = ['key' => $row[1], 'value' => $row[0]];
    }

    $ac = ahocorasick_init($data);
    unset($data);
    unset($fileObject);


    $fileObject2 = new SplFileObject('./keyword.csv', 'r');
    while (!$fileObject2->eof()) {
        $row = $fileObject2->fgetcsv();
        $formattedAreaField = format($row[0]);
        $formattedAreaFieldNoSpace = preg_replace("/( |　)/", "", $formattedAreaField);
        var_dump($formattedAreaFieldNoSpace);
        $result = ahocorasick_match($formattedAreaFieldNoSpace, $ac);
    }
    unset($fileObject2);

    ahocorasick_deinit($ac);

    /**
     * @param string $areaField
     * @return string
     */
    function format($areaField)
    {
        if (preg_match("/ヶ/", $areaField, $matches) === 1) {
            $areaField = str_replace('ヶ', 'ケ', $areaField);
        }

        if (preg_match("/[一-龠々]+ッ[一-龠々]+/u", $areaField, $matches) === 1) {
            $matched = $matches[0];
            $replaced = str_replace('ッ', 'ツ', $matched);
            $areaField = str_replace($matched, $replaced, $areaField);
        }

        if (preg_match("/[一-龠々]+ノ[一-龠々]+/u", $areaField, $matches) === 1) {
            $matched = $matches[0];
            $replaced = str_replace('ノ', 'の', $matched);
            $areaField = str_replace($matched, $replaced, $areaField);
        }

        return $areaField;
    }
