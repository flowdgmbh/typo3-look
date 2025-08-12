<?php

declare(strict_types=1);

namespace Flowd\Look\ViewHelper\Backend\ContentPreview;

use Flowd\Look\Asset\ContentPreviewCssCollector;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Exception;

class CssViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'path',
            'string',
            'Path to the css file',
        );
    }

    public function render(): void
    {
        if (!$this->renderingContext->hasAttribute(ContentPreviewCssCollector::class)) {
            throw new Exception("No ContentPreviewCssCollector found in rendering context.");
        }

        $cssCollector = $this->renderingContext->getAttribute(ContentPreviewCssCollector::class);
        $cssCollector[] = $this->arguments['path'];
    }
}
