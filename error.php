<?php

/**
 * Error page for tpl_joomleague.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\Document\ErrorDocument $this */

$app = Factory::getApplication();

$this->addStyleSheet($this->baseurl . '/media/vendor/bootstrap/css/bootstrap.min.css', ['version' => 'auto']);
$this->addStyleSheet($this->baseurl . '/media/system/css/joomla-fontawesome.min.css', ['version' => 'auto']);
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css', ['version' => 'auto']);
$this->addHeadLink('https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Inter:wght@400;600&display=swap', 'stylesheet');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="jl-tpl">
    <header class="jl-header">
        <div class="jl-header__pitch" aria-hidden="true"></div>
        <div class="jl-header__inner">
            <a class="jl-brand" href="<?php echo $this->baseurl; ?>/">
                <span class="jl-brand__mark"><span class="fa-solid fa-trophy" aria-hidden="true"></span></span>
                <span class="jl-brand__name"><?php echo Text::_('TPL_JOOMLEAGUE_BRAND'); ?></span>
            </a>
        </div>
    </header>
    <div class="jl-layout jl-layout--error">
        <main class="jl-main">
            <div class="jl-error">
                <p class="jl-error__code"><?php echo (int) $this->error->getCode(); ?></p>
                <h1><?php echo Text::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
                <p><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><a class="jl-btn" href="<?php echo $this->baseurl; ?>/"><?php echo Text::_('JERROR_LAYOUT_GO_TO_HOME_PAGE'); ?></a></p>
            </div>
        </main>
    </div>
    <footer class="jl-footer">
        <div class="jl-footer__inner">
            <span class="jl-footer__mark"><span class="fa-solid fa-trophy" aria-hidden="true"></span></span>
            <span><?php echo Text::_('TPL_JOOMLEAGUE_FOOTER'); ?></span>
        </div>
    </footer>
</body>
</html>
