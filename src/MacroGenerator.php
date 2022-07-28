<?php

namespace Infira\Collection;

use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Closure;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\GlobalFunction;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;

class MacroGenerator
{
    public function __construct()
    {
        $this->generateMixins();
        $this->generateIdeHelper();
    }

    /**
     * @return ClassLike[]
     */
    private function getExtensions(): array
    {
        $scan = array_slice(scandir(__DIR__ . '/extensions'), 2);

        return array_map(function ($f) {
            $f = __DIR__ . '/extensions/' . $f;

            return ClassType::fromCode(file_get_contents($f));
        }, $scan);
    }

    private function generateMixins(): void
    {
        $file = new PhpFile();
        $mixinsClass = $file->addNamespace('Infira\Collection')->addClass('CollectionMacros');
        $mixinsClass->addComment('@mixin \Illuminate\Support\Collection');

        foreach ($this->getExtensions() as $class) {
            $extensionMethod = $this->getMethod($class);

            $closure = new Closure();
            $closure->setBody($extensionMethod->getBody());
            $this->mergeParams($closure, $extensionMethod);

            $mixMethod = $mixinsClass->addMethod($extensionMethod->getName())
                ->setStatic(true)
                ->setReturnType('\Closure')
                ->setBody("return $closure;");
        }
        file_put_contents('src/CollectionMacros.php', $file->__toString());
    }

    private function generateIdeHelper(): void
    {
        $file = new PhpFile();
        $file->addComment('This is IDE helper for suggest/autocomplete');
        $file->addComment('@noinspection all');
        $ns = $file->addNamespace('Illuminate\Support');
        $nsClass = $ns->addClass('Collection');

        foreach ($this->getExtensions() as $class) {
            $extensionMethod = $this->getMethod($class);
            $methodName = $extensionMethod->getName();


            $helperMethod = $nsClass->addMethod($methodName)
                ->setReturnType('static')
                ->setComment($extensionMethod->getComment())
                ->setBody('/** @see \Infira\Collection\extensions\\' . $class->getName() . '::' . $methodName . '() */;');

            $this->mergeParams($helperMethod, $extensionMethod);
        }
        file_put_contents('src/_ide_helper.php', $file->__toString());
    }

    private function mergeParams(Method|Closure $toMethod, Method $fromMethod): void
    {
        foreach ($fromMethod->getParameters() as $parameter) {
            $helperParameter = $toMethod->addParameter($parameter->getName());
            if ($t = $parameter->getType()) {
                $helperParameter->setType($t);
            }
        }
    }

    private function getMethod(ClassLike $class): Method
    {
        return array_values($class->getMethods())[0];
    }

    private function addMacro() {}
}