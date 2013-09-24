<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web\ContentDataCompiler;;

/**
 * Content data compiler factory
 */
class CompilerFactory
{
    /**
     * @var array|CompilerInterface[]
     */
    protected static $compilers = array();

    /**
     * @var bool
     */
    protected static $init = false;

    /**
     * Initialize (Add defaults)
     */
    protected static function init()
    {
        if (self::$init) {
            return;
        }

        self::$compilers['xml'] = new XmlCompiler();
        self::$compilers['json'] = new JsonCompiler();
        self::$compilers['native'] = new NativeCompiler();

        self::$init = true;
    }

    /**
     * Add compiler to storage
     *
     * @param string $name
     * @param CompilerInterface $compiler
     */
    public static function add($name, CompilerInterface $compiler)
    {
        self::$compilers[strtolower($name)] = $compiler;
    }

    /**
     * Get compiler
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return CompilerInterface
     */
    public static function get($name)
    {
        $name = strtolower($name);

        if (!isset(self::$compilers[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Undefined compiler with name "%s".',
                $name
            ));
        }

        return self::$compilers[$name];
    }

    /**
     * Has compiler
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {
        return isset(self::$compilers[$name]);
    }

    /**
     * Compile content data
     *
     * @param string|CompilerInterface $compiler
     * @param mixed $data
     * @return string
     */
    public static function compile($compiler, $data)
    {
        self::init();

        if (!$compiler) {
            // Try get compiler from data type
            switch (true) {
                case $data instanceof \DOMDocument:
                    $compiler = 'xml';
                    break;

                case $data instanceof \JsonSerializable:
                    $compiler = 'json';
                    break;

                default:
                    $compiler = 'native';
            }
        }

        if (!$compiler instanceof CompilerInterface) {
            $compiler = self::get($compiler);
        }

        return $compiler->compile($data);
    }
}