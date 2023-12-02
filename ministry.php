<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

//Ministry Class
class Ministry {
    private $iblockId;

    public function __construct($iblockId) {
        $this->iblockId = $iblockId;
    }

    public function getEmployeesByMinistry($ministryName) {
        $employees = array();

        // Получение ID раздела министерства по его названию
        $rsSection = CIBlockSection::GetList(
            array('SORT' => 'ASC'),
            array('IBLOCK_ID' => $this->iblockId, 'NAME' => $ministryName),
            false,
            array('ID')
        );

        if ($arSection = $rsSection->Fetch()) {
            $sectionId = $arSection['ID'];

            // Поиск всех элементов инфоблока с привязкой к текущему разделу
            $rsElements = CIBlockElement::GetList(
                array('SORT' => 'ASC'),
                array('IBLOCK_ID' => $this->iblockId, 'SECTION_ID' => $sectionId),
                false,
                false,
                array('ID', 'NAME', 'PROPERTY_UF_SOTRUDNIK')
            );

            while ($arElement = $rsElements->Fetch()) {
                $employee = new Employee($arElement['NAME'], $arElement['PROPERTY_UF_SOTRUDNIK']);
                $employees[] = $employee;
            }
        }

        return $employees;
    }

    public function getMinistries() {
        $ministries = array();
        $rsSections = CIBlockSection::GetList(
            array('SORT' => 'ASC'),
            array('IBLOCK_ID' => $this->iblockId)
        );

        while ($arSection = $rsSections->Fetch()) {
            $ministries[] = $arSection['NAME'];
        }

        return $ministries;
    }
}
// $iblockId необходимо определять динамически, для это в каждой компании свой способ
$iblockId = 1;
// Создание объекта Ministry
$ministryObj = new Ministry($iblockId);

// Получение списка министерств
$ministries = $ministryObj->getMinistries();

// Вывод списка министерств
foreach ($ministries as $ministry) {
    echo $ministry . "<br>";
}

//Employee Class
class Employee {
    private $name;
    private $subMinistryName;

    public function __construct($name, $subMinistryName) {
        $this->name = $name;
        $this->subMinistryName = $subMinistryName;
    }

    public function getName() {
        return $this->name;
    }

    public function getSubMinistryName() {
        return $this->subMinistryName;
    }
}

//User Class
class User
{
    private $associatedEmployee;

    public function setAssociatedEmployee(Employee $employee)
    {
        $this->associatedEmployee = $employee;
    }

    public function getAssociatedEmployee()
    {
        return $this->associatedEmployee;
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");