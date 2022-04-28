<?php

namespace App\Service\Currency\DataSource;

interface ICurrencySource
{

    public function loadAllFiles(): array;

    /**
     * @param \DateTime|null $date
     * @param bool $shouldSave
     */
    public function readDate(\DateTime $date = null, bool $shouldSave = true);

    public function saveDate(\DateTime $date, array $data): \SplFileInfo;

    public static function getFolder(): string;

}