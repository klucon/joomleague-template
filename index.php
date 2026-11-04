<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */
$app = Factory::getApplication();
$menu = $app->getMenu()->getActive();
$input = $app->getInput();
$defaultItem = max(1, (int) $app->getMenu()->getDefault()?->id);
$homeItem = max(1, (int) $this->params->get('home_item', $defaultItem));
$tableItem = max(1, (int) $this->params->get('table_item', $homeItem));
$primaryItem = max(1, (int) $this->params->get('hero_primary_item', $homeItem));
$secondaryItem = max(1, (int) $this->params->get('hero_secondary_item', $tableItem));
$isPositionPreview = $input->getBool('tp')
	&& (bool) ComponentHelper::getParams('com_templates')->get('template_positions_display');
$requestUri = Uri::getInstance();
$requestPath = trim((string) $requestUri->getPath(), '/');
$basePath = trim((string) Uri::root(true), '/');

if ($basePath !== '' && ($requestPath === $basePath || str_starts_with($requestPath, $basePath . '/'))) {
	$requestPath = trim(substr($requestPath, strlen($basePath)), '/');
}

$rawQuery = $requestUri->getQuery(true);
$hasExplicitComponentRoute = isset($rawQuery['option'])
	|| isset($rawQuery['view'])
	|| isset($rawQuery['project_id']);
$isHomeRequest = $requestPath === ''
	|| ($requestPath === 'index.php' && !$hasExplicitComponentRoute);
$isHome = $isPositionPreview || ($isHomeRequest
	&& (int) ($menu?->id ?? 0) === $homeItem
	&& $input->getCmd('option') === 'com_joomleague'
	&& $input->getCmd('view', 'project') === 'project'
	&& $input->getInt('project_id', 1) > 0);
$app->getLanguage()->load($this->template, JPATH_THEMES . '/' . $this->template);
$textParam = fn(string $name, string $fallback): string => trim((string) $this->params->get($name, '')) ?: Text::_($fallback);
$colour = static fn(mixed $value, string $fallback): string => preg_match('/^#[0-9a-f]{6}$/i', (string) $value) ? (string) $value : $fallback;
$baseUrl = $this->baseurl;
$mediaUrl = static function (mixed $value, string $fallback = '') use ($baseUrl): string {
	$path = trim(explode('#', (string) $value, 2)[0]);
	if ($path === '' || preg_match('#^(?:[a-z]+:)?//#i', $path) || str_contains($path, '..')) return $fallback;
	return $baseUrl . '/' . ltrim($path, '/');
};
$brandName = $textParam('brand_name', 'TPL_JOOMLEAGUE_BRAND');
$brandSport = $textParam('brand_sport', 'TPL_JOOMLEAGUE_BRAND_SPORT');
$brandLogo = $mediaUrl($this->params->get('brand_logo'));
$heroImage = $mediaUrl($this->params->get('hero_image'), $this->baseurl . '/templates/' . $this->template . '/images/hero-default.png');
$iconMap = ['football' => 'fa-futbol', 'basketball' => 'fa-basketball', 'hockey' => 'fa-hockey-puck', 'trophy' => 'fa-trophy'];
$brandIcon = $iconMap[(string) $this->params->get('brand_icon', 'trophy')] ?? $iconMap['trophy'];
$contentWidths = [960, 1140, 1200, 1320, 1440, 1600, 1920];
$contentWidth = (int) $this->params->get('content_width', 1320);
$contentWidth = in_array($contentWidth, $contentWidths, true) ? $contentWidth : 1320;
$rootStyle = implode(';', [
	'--jl-green:' . $colour($this->params->get('colour_primary'), '#22a85a'),
	'--jl-green-dark:' . $colour($this->params->get('colour_primary_dark'), '#12683a'),
	'--jl-lime:' . $colour($this->params->get('colour_accent'), '#d5f04d'),
	'--jl-night:' . $colour($this->params->get('colour_dark'), '#07130e'),
	'--jl-ink:' . $colour($this->params->get('colour_text'), '#15241c'),
	'--jl-surface:' . $colour($this->params->get('colour_surface'), '#f3f6f4'),
	'--jl-shell:' . $contentWidth . 'px',
	'--jl-hero-image:url(&quot;' . htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') . '&quot;)',
]);
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$templatePath = JPATH_THEMES . '/' . $this->template;
$cssVersion = (string) filemtime($templatePath . '/css/template.css');
$jsVersion = (string) filemtime($templatePath . '/js/template.js');
$this->addStyleSheet($this->baseurl . '/media/vendor/bootstrap/css/bootstrap.min.css', ['version' => 'auto']);
$this->addStyleSheet($this->baseurl . '/media/system/css/joomla-fontawesome.min.css', ['version' => 'auto']);
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css', ['version' => $cssVersion]);
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/template.js', ['version' => $jsVersion], ['defer' => true]);
$this->addHeadLink('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap', 'stylesheet');
$favicon = $mediaUrl($this->params->get('favicon'));
$this->addHeadLink($favicon !== '' ? $favicon : HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head><jdoc:include type="metas" /><jdoc:include type="styles" /><jdoc:include type="scripts" /></head>
<body class="jl-tpl <?php echo $isHome ? 'jl-home' : 'jl-inner'; ?>" style="<?php echo $rootStyle; ?>">
	<a class="jl-skip" href="#jl-content"><?php echo Text::_('TPL_JOOMLEAGUE_SKIP'); ?></a>
	<?php if ($isPositionPreview) : ?>
	<main id="jl-content" class="jl-position-preview">
		<jdoc:include type="modules" name="utility-links" style="none" />
		<jdoc:include type="modules" name="search" style="none" />
		<jdoc:include type="modules" name="menu" style="none" />
		<jdoc:include type="modules" name="competition-nav" style="none" />
		<jdoc:include type="modules" name="programme-ticker" style="none" />
		<jdoc:include type="modules" name="hero-event" style="none" />
		<jdoc:include type="modules" name="breadcrumbs" style="none" />
		<jdoc:include type="modules" name="news-lead" style="none" />
		<jdoc:include type="modules" name="league-main" style="none" />
		<jdoc:include type="modules" name="calendar-main" style="none" />
		<jdoc:include type="modules" name="league-side" style="none" />
		<jdoc:include type="modules" name="rankings" style="none" />
		<jdoc:include type="modules" name="club-showcase" style="none" />
		<jdoc:include type="modules" name="venue-programme" style="none" />
		<jdoc:include type="modules" name="discover" style="none" />
		<jdoc:include type="modules" name="mail" style="none" />
		<jdoc:include type="modules" name="footer-a" style="none" />
		<jdoc:include type="modules" name="footer-b" style="none" />
		<jdoc:include type="modules" name="footer-c" style="none" />
	</main>
	<?php else : ?>
	<?php if ($isPositionPreview || (int) $this->params->get('show_utility', 1) === 1) : ?>
	<div class="jl-utility"><div class="jl-shell jl-utility__inner">
		<div class="jl-utility__status"><span class="jl-live-dot" aria-hidden="true"></span><?php echo Text::_('TPL_JOOMLEAGUE_SEASON_STATUS'); ?></div>
		<?php if ($this->countModules('utility-links', true)) : ?><div class="jl-utility__links"><jdoc:include type="modules" name="utility-links" style="none" /></div><?php endif; ?>
		<?php if ($this->countModules('search', true)) : ?><div class="jl-search"><jdoc:include type="modules" name="search" style="none" /></div><?php endif; ?>
	</div></div>
	<?php endif; ?>
	<header class="jl-site-header">
		<div class="jl-shell jl-site-header__main">
			<a class="jl-brand" href="<?php echo $this->baseurl; ?>/index.php?Itemid=<?php echo $homeItem; ?>"><?php if ($brandLogo !== '') : ?><img class="jl-brand__logo" src="<?php echo htmlspecialchars($brandLogo, ENT_QUOTES, 'UTF-8'); ?>" alt=""><?php else : ?><span class="jl-brand__mark" aria-hidden="true"><span class="fa-solid <?php echo $brandIcon; ?>"></span></span><?php endif; ?><span><span class="jl-brand__name"><?php echo htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8'); ?></span><span class="jl-brand__sport"><?php echo htmlspecialchars($brandSport, ENT_QUOTES, 'UTF-8'); ?></span></span></a>
			<button class="jl-menu-toggle" type="button" aria-expanded="false" aria-controls="jl-primary-nav"><span class="fa-solid fa-bars" aria-hidden="true"></span><span><?php echo Text::_('TPL_JOOMLEAGUE_MENU'); ?></span></button>
			<?php if ($this->countModules('menu', true)) : ?><nav id="jl-primary-nav" class="jl-primary-nav" aria-label="<?php echo Text::_('TPL_JOOMLEAGUE_SITE_NAVIGATION'); ?>"><jdoc:include type="modules" name="menu" style="none" /></nav><?php endif; ?>
		</div>
		<?php if (($isPositionPreview || (int) $this->params->get('show_competition_nav', 1) === 1) && $this->countModules('competition-nav', true)) : ?><div class="jl-competition-nav"><div class="jl-shell"><jdoc:include type="modules" name="competition-nav" style="none" /></div></div><?php endif; ?>
	</header>
	<?php if ($isHome && (int) $this->params->get('show_hero', 1) === 1) : ?>
		<section class="jl-hero"><div class="jl-shell jl-hero__inner">
			<div class="jl-hero__content"><p class="jl-kicker"><?php echo htmlspecialchars($textParam('hero_kicker', 'TPL_JOOMLEAGUE_HERO_KICKER'), ENT_QUOTES, 'UTF-8'); ?></p><h1><?php echo htmlspecialchars($textParam('hero_title', 'TPL_JOOMLEAGUE_HERO_TITLE'), ENT_QUOTES, 'UTF-8'); ?></h1><p class="jl-hero__lead"><?php echo htmlspecialchars($textParam('hero_copy', 'TPL_JOOMLEAGUE_HERO_COPY'), ENT_QUOTES, 'UTF-8'); ?></p><div class="jl-hero__actions"><a class="jl-button jl-button--primary" href="index.php?Itemid=<?php echo $primaryItem; ?>"><?php echo htmlspecialchars($textParam('hero_primary_label', 'TPL_JOOMLEAGUE_RESULTS_CTA'), ENT_QUOTES, 'UTF-8'); ?></a><a class="jl-button jl-button--ghost" href="index.php?Itemid=<?php echo $secondaryItem; ?>"><?php echo htmlspecialchars($textParam('hero_secondary_label', 'TPL_JOOMLEAGUE_TABLE_CTA'), ENT_QUOTES, 'UTF-8'); ?></a></div></div>
			<?php if ($this->countModules('hero-event', true)) : ?><aside class="jl-hero__event" aria-label="<?php echo Text::_('TPL_JOOMLEAGUE_NEXT_EVENT'); ?>"><jdoc:include type="modules" name="hero-event" style="html5" /></aside><?php endif; ?>
		</div></section>
	<?php elseif (!$isHome) : ?><div class="jl-page-banner"><div class="jl-shell"><p><?php echo Text::_('TPL_JOOMLEAGUE_MATCHDAY'); ?></p><strong><?php echo htmlspecialchars((string) ($menu?->title ?? $brandSport), ENT_QUOTES, 'UTF-8'); ?></strong></div></div><?php endif; ?>
	<?php if (($isPositionPreview || (int) $this->params->get('show_ticker', 1) === 1) && $this->countModules('programme-ticker', true)) : ?><div class="jl-competition-nav"><div class="jl-shell"><jdoc:include type="modules" name="programme-ticker" style="none" /></div></div><?php endif; ?>
	<?php if (($isPositionPreview || (int) $this->params->get('show_breadcrumbs', 1) === 1) && $this->countModules('breadcrumbs', true)) : ?><div class="jl-breadcrumbs"><div class="jl-shell"><jdoc:include type="modules" name="breadcrumbs" style="none" /></div></div><?php endif; ?>
	<main id="jl-content">
		<jdoc:include type="message" />
		<?php if ($isHome) : ?>
			<section class="jl-home-dashboard"><div class="jl-shell jl-home-dashboard__grid">
				<div class="jl-home-dashboard__main">
					<?php if ($this->countModules('news-lead', true)) : ?><section class="jl-home-block jl-news"><div class="jl-news-grid"><jdoc:include type="modules" name="news-lead" style="html5" /></div></section><?php endif; ?>
					<?php if ($this->countModules('league-main', true)) : ?><section class="jl-home-block"><jdoc:include type="modules" name="league-main" style="html5" /></section><?php endif; ?>
					<?php if ($this->countModules('calendar-main', true)) : ?><section class="jl-home-block"><jdoc:include type="modules" name="calendar-main" style="html5" /></section><?php endif; ?>
					<?php if ($this->countModules('venue-programme', true)) : ?><section class="jl-home-block"><jdoc:include type="modules" name="venue-programme" style="html5" /></section><?php endif; ?>
				</div>
				<aside class="jl-home-dashboard__rail">
					<?php if ($this->countModules('league-side', true)) : ?><section class="jl-rail-block"><jdoc:include type="modules" name="league-side" style="html5" /></section><?php endif; ?>
					<?php if ($this->countModules('rankings', true)) : ?><section class="jl-rail-block"><div class="jl-rankings-grid"><jdoc:include type="modules" name="rankings" style="html5" /></div></section><?php endif; ?>
				</aside>
			</div></section>
			<?php if ($this->countModules('club-showcase', true)) : ?><section class="jl-section jl-club-section"><div class="jl-shell"><div class="jl-showcase-grid"><jdoc:include type="modules" name="club-showcase" style="html5" /></div></div></section><?php endif; ?>
			<?php if ($this->countModules('discover', true)) : ?><section class="jl-section jl-discover-section"><div class="jl-shell"><div class="jl-discover-grid"><jdoc:include type="modules" name="discover" style="html5" /></div></div></section><?php endif; ?>
		<?php else : ?>
			<section class="jl-section jl-component-section"><div class="jl-shell"><jdoc:include type="component" /></div></section>
		<?php endif; ?>
		<?php if ($isHome && ($isPositionPreview || (int) $this->params->get('show_mail', 1) === 1) && $this->countModules('mail', true)) : ?><section class="jl-mail"><div class="jl-shell"><jdoc:include type="modules" name="mail" style="none" /></div></section><?php endif; ?>
	</main>
	<?php if ($isPositionPreview || (int) $this->params->get('show_footer', 1) === 1) : ?>
	<footer class="jl-footer"><div class="jl-shell jl-footer__grid"><div class="jl-footer__brand"><?php if ($brandLogo !== '') : ?><img class="jl-brand__logo" src="<?php echo htmlspecialchars($brandLogo, ENT_QUOTES, 'UTF-8'); ?>" alt=""><?php else : ?><span class="jl-brand__mark"><span class="fa-solid <?php echo $brandIcon; ?>" aria-hidden="true"></span></span><?php endif; ?><strong><?php echo htmlspecialchars($textParam('footer_brand', 'TPL_JOOMLEAGUE_FOOTER_BRAND'), ENT_QUOTES, 'UTF-8'); ?></strong><p><?php echo htmlspecialchars($textParam('footer_copy', 'TPL_JOOMLEAGUE_FOOTER_COPY'), ENT_QUOTES, 'UTF-8'); ?></p><?php
		$socialLinks = $this->params->get('social_links', []);
		$networkIcons = ['facebook' => 'fa-brands fa-facebook-f', 'instagram' => 'fa-brands fa-instagram', 'youtube' => 'fa-brands fa-youtube', 'x' => 'fa-brands fa-x-twitter', 'linkedin' => 'fa-brands fa-linkedin-in', 'website' => 'fa-solid fa-link'];
		if (is_object($socialLinks)) $socialLinks = (array) $socialLinks;
		if (is_array($socialLinks) && $socialLinks !== []) : ?><div class="jl-footer__social" aria-label="<?php echo Text::_('TPL_JOOMLEAGUE_SOCIAL_LINKS'); ?>"><?php foreach ($socialLinks as $social) : $social = is_object($social) ? (array) $social : $social; $network = (string) ($social['network'] ?? 'website'); $url = filter_var((string) ($social['url'] ?? ''), FILTER_VALIDATE_URL); if (!$url || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) continue; ?><a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars(ucfirst($network), ENT_QUOTES, 'UTF-8'); ?>"><span class="<?php echo $networkIcons[$network] ?? 'fa-solid fa-link'; ?>" aria-hidden="true"></span></a><?php endforeach; ?></div><?php endif; ?></div><?php foreach (['footer-a', 'footer-b', 'footer-c'] as $position) : ?><?php if ($this->countModules($position, true)) : ?><div class="jl-footer__column"><jdoc:include type="modules" name="<?php echo $position; ?>" style="html5" /></div><?php endif; ?><?php endforeach; ?></div><div class="jl-footer__bottom"><div class="jl-shell"><span><?php echo htmlspecialchars($textParam('footer_bottom', 'TPL_JOOMLEAGUE_FOOTER'), ENT_QUOTES, 'UTF-8'); ?></span><?php $footerUrl = filter_var((string) $this->params->get('footer_link_url', 'https://joomleague.eu'), FILTER_VALIDATE_URL); if ($footerUrl) : ?><a href="<?php echo htmlspecialchars($footerUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars((string) $this->params->get('footer_link_label', 'joomleague.eu'), ENT_QUOTES, 'UTF-8'); ?></a><?php endif; ?></div></div></footer>
	<?php endif; ?>
	<button class="jl-to-top" type="button" aria-label="<?php echo Text::_('TPL_JOOMLEAGUE_TOP'); ?>"><span class="fa-solid fa-arrow-up" aria-hidden="true"></span></button>
	<?php endif; ?>
</body>
</html>
