<?php

namespace Magecomp\Adminactivity\Model\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;

/**
 * Class SchemaLocator
 * @package Magecomp\Adminactivity\Model\Config
 */
class Schemalocator implements SchemaLocatorInterface
{
    /**
     * XML schema for config file.
     */
    const CONFIG_FILE_SCHEMA = 'activitylogdetail.xsd';

    /**
     * Path to corresponding XSD file with validation rules for merged config
     * @var string
     */
    public $schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     * @var string
     */
    public $perFileSchema = null;

    /**
     * SchemaLocator constructor.
     * @param Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $configDir = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Magecomp_Adminactivity');
        $this->schema = $configDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_SCHEMA;
        $this->perFileSchema = $configDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_SCHEMA;
    }

    /**
     * Get schema
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get file schema
     * @return string
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
