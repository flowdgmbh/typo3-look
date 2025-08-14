<?php

declare(strict_types=1);

namespace Flowd\Look\ViewHelper\Backend;

use Flowd\Look\Asset\ContentPreviewCssCollector;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class ContentPreviewViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        protected readonly FlashMessageService $flashMessageService
    ) {}

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'height',
            'integer',
            'Limit the height of the preview in pixel',
        );

        $this->registerArgument(
            'scale',
            'double',
            'Scaling of the preview',
            false,
            0.5
        );
    }

    public function render(): ?string
    {
        try {
            $contentPreviewCssCollector = new ContentPreviewCssCollector([
                'EXT:look/Resources/Public/Css/Backend/iFramePreview.css'
            ]);

            $this->renderingContext->setAttribute(
                ContentPreviewCssCollector::class,
                $contentPreviewCssCollector
            );

            $viewFactoryData = new ViewFactoryData(
                templateRootPaths: ['EXT:look/Resources/Private/Templates/Backend/ContentPreview/'],
                request: $this->renderingContext->getAttribute(ServerRequestInterface::class),
            );

            $view = GeneralUtility::makeInstance(ViewFactoryInterface::class)->create($viewFactoryData);
            $view->assignMultiple([
                'scale' => $this->arguments['scale'],
                'height' => $this->arguments['height'],
                'previewContent' => $this->renderChildren(),
                'assets' => [
                    'css' => $contentPreviewCssCollector,
                ]
            ]);

            $style = 'width:100%;';
            $style .= 'pointer-events: none;';

            $height = (int)($this->arguments['height'] ?? 0);
            if ($height > 0) {
                $style .= sprintf('max-height: %dpx;', $height);
            }

            $tagBuilder = new TagBuilder('iframe');
            $tagBuilder->addAttribute('style', $style);
            $tagBuilder->addAttribute('srcdoc', trim($view->render('IFramePreview')));
            $tagBuilder->addAttribute('allow', '');
            $tagBuilder->addAttribute('loading', 'lazy');
            $tagBuilder->forceClosingTag(true);

            return $tagBuilder->render();
        } catch (\Exception $e) {
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                'The following error occurred: ' . $e->getMessage(),
                'Preview could not be rendered: ',
                ContextualFeedbackSeverity::WARNING
            );
            $this->flashMessageService->getMessageQueueByIdentifier('flowd.look.preview')->enqueue($flashMessage);

            return null;
        }
    }
}
