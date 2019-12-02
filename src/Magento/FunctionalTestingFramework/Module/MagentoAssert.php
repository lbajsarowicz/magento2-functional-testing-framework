<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class MagentoAssert
 *
 * Contains all custom assert functions to be used in tests.
 *
 * @package Magento\FunctionalTestingFramework\Module
 */
class MagentoAssert extends \Codeception\Module
{
    /**
     * PersistedObjectHandler instance
     *
     * @var PersistedObjectHandler
     */
    private static $persistHandler = null;

    /**
     * Asserts that all items in the array are sorted by given direction. Can be given int, string, double, dates.
     * Converts given date strings to epoch for comparison.
     *
     * @param array  $data
     * @param string $sortOrder
     * @return void
     */
    public function assertArrayIsSorted(array $data, $sortOrder = "asc")
    {
        $elementTotal = count($data);
        $message = null;

        // If value can be converted to a date and it isn't 1.1 number (strtotime is overzealous)
        if (strtotime($data[0]) !== false && !is_numeric($data[0])) {
            $message = "Array of dates converted to unix timestamp for comparison";
            $data = array_map('strtotime', $data);
        } else {
            $data = array_map('strtolower', $data);
        }

        if ($sortOrder == "asc") {
            for ($i = 1; $i < $elementTotal; $i++) {
                // $i >= $i-1
                $this->assertLessThanOrEqual($data[$i], $data[$i-1], $message);
            }
        } else {
            for ($i = 1; $i < $elementTotal; $i++) {
                // $i <= $i-1
                $this->assertGreaterThanOrEqual($data[$i], $data[$i-1], $message);
            }
        }
    }

    /**
     * Create an entity
     *
     * @param string $key                 StepKey of the createData action.
     * @param string $scope
     * @param string $entity              Name of xml entity to create.
     * @param array  $dependentObjectKeys StepKeys of other createData actions that are required.
     * @param array  $overrideFields      Array of FieldName => Value of override fields.
     * @param string $storeCode
     * @return void
     */
    public function createEntity(
        $key,
        $scope,
        $entity,
        $dependentObjectKeys = [],
        $overrideFields = [],
        $storeCode = ''
    ) {
        if (!self::$persistHandler) {
            self::$persistHandler = PersistedObjectHandler::getInstance();
        }

        self::$persistHandler->createEntity(
            $key,
            $scope,
            $entity,
            $dependentObjectKeys,
            $overrideFields,
            $storeCode
        );
    }

    /**
     * Retrieves and updates a previously created entity
     *
     * @param string $key                 StepKey of the createData action.
     * @param string $scope
     * @param string $updateEntity        Name of the static XML data to update the entity with.
     * @param array  $dependentObjectKeys StepKeys of other createData actions that are required.
     * @return void
     */
    public function updateEntity($key, $scope, $updateEntity, $dependentObjectKeys = [])
    {
        if (!self::$persistHandler) {
            self::$persistHandler = PersistedObjectHandler::getInstance();
        }

        self::$persistHandler->updateEntity(
            $key,
            $scope,
            $updateEntity,
            $dependentObjectKeys
        );
    }

    /**
     * Performs GET on given entity and stores entity for use
     *
     * @param string  $key                 StepKey of getData action.
     * @param string  $scope
     * @param string  $entity              Name of XML static data to use.
     * @param array   $dependentObjectKeys StepKeys of other createData actions that are required.
     * @param string  $storeCode
     * @param integer $index
     * @return void
     */
    public function getEntity($key, $scope, $entity, $dependentObjectKeys = [], $storeCode = '', $index = null)
    {
        if (!self::$persistHandler) {
            self::$persistHandler = PersistedObjectHandler::getInstance();
        }

        self::$persistHandler->getEntity(
            $key,
            $scope,
            $entity,
            $dependentObjectKeys,
            $storeCode,
            $index
        );
    }

    /**
     * Retrieves and deletes a previously created entity
     *
     * @param string $key   StepKey of the createData action.
     * @param string $scope
     * @return void
     */
    public function deleteEntity($key, $scope)
    {
        if (!self::$persistHandler) {
            self::$persistHandler = PersistedObjectHandler::getInstance();
        }

        self::$persistHandler->deleteEntity($key, $scope);
    }

    /**
     * Retrieves a field from an entity, according to key and scope given
     *
     * @param string $stepKey
     * @param string $field
     * @param string $scope
     * @return string
     */
    public function retrieveEntityField($stepKey, $field, $scope)
    {
        if (!self::$persistHandler) {
            self::$persistHandler = PersistedObjectHandler::getInstance();
        }

        return self::$persistHandler->retrieveEntityField($stepKey, $field, $scope);
    }

    /**
     * Get encrypted value by key
     *
     * @param string $key
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getSecret($key)
    {
        return CredentialStore::getInstance()->getSecret($key);
    }
}
