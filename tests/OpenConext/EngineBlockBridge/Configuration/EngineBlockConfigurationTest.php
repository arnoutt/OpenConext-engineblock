<?php

namespace OpenConext\EngineBlockBridge\Tests;

use OpenConext\EngineBlock\Exception\InvalidArgumentException;
use OpenConext\EngineBlockBridge\Configuration\EngineBlockConfiguration;
use PHPUnit_Framework_TestCase as TestCase;

class EngineBlockConfigurationTest extends TestCase
{
    /**
     * @test
     * @group EngineBlockBridge
     */
    public function configuration_can_be_created_from_multidimensional_array()
    {
        $configuredValues = array('path' => array('sub_path' => 'value'));

        $expectedConfiguration = new EngineBlockConfiguration(
            array(
                'path' => new EngineBlockConfiguration(
                    array(
                        'sub_path' => 'value'
                    )
                )
            )
        );

        $actualConfiguration = new EngineBlockConfiguration($configuredValues);

        $this->assertEquals($expectedConfiguration, $actualConfiguration);
    }

    /**
     * @test
     * @group EngineBlockBridge
     *
     * @dataProvider \OpenConext\TestDataProvider::notStringOrEmptyString
     * @expectedException InvalidArgumentException
     *
     * @param mixed $path
     */
    public function non_string_or_non_empty_string_path_cannot_be_used_for_querying($path)
    {
        $configuration = new EngineBlockConfiguration(array());
        $configuration->get($path);
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function default_value_is_retrieved_when_configuration_path_cannot_be_found()
    {
        $defaultValue  = 'some_default_value';
        $configuration = new EngineBlockConfiguration(array());

        $retrievedValue = $configuration->get('not_configured_key', $defaultValue);

        $this->assertSame($defaultValue, $retrievedValue);
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function default_value_is_retrieved_when_nested_configuration_path_cannot_be_found()
    {
        $defaultValue  = 'some_default_value';
        $configuration = new EngineBlockConfiguration(array('path' => array('sub_path' => 'not_retrieved_value')));

        $retrievedValue = $configuration->get('path.not_configured_key', $defaultValue);

        $this->assertSame($defaultValue, $retrievedValue);
    }

    /**
     * @test
     * @group EngineBlockBridge
     *
     * @dataProvider \OpenConext\TestDataProvider::nullOrScalar
     *
     * @param mixed $configuredValue
     */
    public function configured_scalars_or_null_can_be_retrieved_by_path($configuredValue)
    {
        $path           = 'path_to_value';
        $configuration  = new EngineBlockConfiguration(array($path => $configuredValue));
        $retrievedValue = $configuration->get($path);

        $this->assertSame($configuredValue, $retrievedValue);
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function sub_configuration_can_be_retrieved_by_path()
    {
        $configuredSubConfig = array('path_to_value' => 'a_configured_value');
        $subConfigPath       = 'sub_config_path';
        $configuration       = new EngineBlockConfiguration(array($subConfigPath => $configuredSubConfig));

        $retrievedSubConfig = $configuration->get($subConfigPath);

        $this->assertInstanceOf(
            '\OpenConext\EngineBlockBridge\Configuration\EngineBlockConfiguration',
            $retrievedSubConfig
        );
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function nested_value_can_be_retrieved_by_path()
    {
        $configuredValue = 'some_configured_value';
        $nestedConfig    = array(
            'path' => array(
                'sub_path' => $configuredValue
            )
        );
        $configuration   = new EngineBlockConfiguration($nestedConfig);

        $retrievedValue = $configuration->get('path.sub_path');

        $this->assertEquals($configuredValue, $retrievedValue);
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function configuration_can_be_converted_to_array()
    {
        $configArray = array('key_a' => 'value_a', 'key_b' => 'value_b');

        $configuration          = new EngineBlockConfiguration($configArray);
        $arrayFromConfiguration = $configuration->toArray();

        $this->assertEquals($configArray, $arrayFromConfiguration);
    }

    /**
     * @test
     * @group EngineBlockBridge
     */
    public function nested_configuration_can_be_converted_to_array()
    {
        $configArray = array(
            'key_a' => 'value_a',
            'key_b' => array(
                'nested_key' => 'nested_value'
            )
        );

        $configuration          = new EngineBlockConfiguration($configArray);
        $arrayFromConfiguration = $configuration->toArray();

        $this->assertEquals($configArray, $arrayFromConfiguration);
    }
}