<?php

namespace Rsvpify\LaravelInky;

use IncentFit\Inky\Inky;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;

class InkyCompiler extends Compiler implements CompilerInterface
{
    protected Inky $inky;

    protected ?string $path = null;

    public function __construct(protected BladeCompiler $blade, Filesystem $files, string $cachePath)
    {
        parent::__construct($files, $cachePath);

        $this->inky = new Inky;
    }

    public function compile($path = null): void
    {
        if ($path) {
            $this->setPath($path);
        }

        if (! is_null($this->cachePath)) {
            $contents = $this->compileString($this->files->get($this->getPath()));

            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function compileString(string $value): string
    {
        return $this->blade->compileString($this->inky->releaseTheKraken($value));
    }

    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    public function getBlade(): BladeCompiler
    {
        return $this->blade;
    }
}
