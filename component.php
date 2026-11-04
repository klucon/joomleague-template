<?php

/**
 * Raw component-only rendering (print/modal contexts) for tpl_joomleague.
 */

defined('_JEXEC') or die;

/** @var Joomla\CMS\Document\HtmlDocument $this */

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
<body class="jl-tpl jl-tpl--component">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
