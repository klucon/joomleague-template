<?php

declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var array<string,mixed> $navigation */
$primaryKeys = ['overview', 'program', 'standings', 'participants', 'statistics'];
$currentView = Factory::getApplication()->getInput()->getCmd('view', 'project');
$viewKeys = [
	'project' => 'overview', 'results' => 'program', 'standings' => 'standings',
	'participants' => 'participants', 'statisticsoverview' => 'statistics',
	'personnel' => 'personnel', 'bracket' => 'bracket', 'resultmatrix' => 'matrix',
	'comparison' => 'comparison', 'standingprogression' => 'progression',
];
$currentKey = $viewKeys[$currentView] ?? '';
$items = $navigation['items'] ?? [];
$primary = array_values(array_filter($items, static fn(array $item): bool => in_array($item['key'], $primaryKeys, true)));
$more = array_values(array_filter($items, static fn(array $item): bool => !in_array($item['key'], $primaryKeys, true)));
?>
<?php if (isset($navigation['error'])) : ?>
	<div class="alert alert-info mb-0"><?php echo Text::_((string) $navigation['error']); ?></div>
<?php elseif ($items !== []) : ?>
	<nav class="jl-dock" aria-label="<?php echo Text::_('MOD_JOOMLEAGUE_NAVIGATION_LABEL'); ?>">
		<div class="jl-dock__identity">
			<span><?php echo Text::_('TPL_JOOMLEAGUE_DOCK_LABEL'); ?></span>
			<strong><?php echo htmlspecialchars((string) $navigation['project']->name, ENT_QUOTES, 'UTF-8'); ?></strong>
		</div>
		<div class="jl-dock__primary">
			<?php foreach ($primary as $item) : ?>
				<a class="jl-dock__link<?php echo $currentKey === $item['key'] ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo $currentKey === $item['key'] ? ' aria-current="page"' : ''; ?>><span class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></span><span><?php echo Text::_($item['label']); ?></span></a>
			<?php endforeach; ?>
		</div>
		<?php if ($more !== []) : ?>
			<details class="jl-dock__more"<?php echo in_array($currentKey, array_column($more, 'key'), true) ? ' open' : ''; ?>>
				<summary><span class="icon-ellipsis-h" aria-hidden="true"></span><?php echo Text::_('TPL_JOOMLEAGUE_DOCK_MORE'); ?></summary>
				<div class="jl-dock__more-panel">
					<?php foreach ($more as $item) : ?>
						<a class="jl-dock__link<?php echo $currentKey === $item['key'] ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo $currentKey === $item['key'] ? ' aria-current="page"' : ''; ?>><span class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></span><span><?php echo Text::_($item['label']); ?></span></a>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endif; ?>
	</nav>
<?php endif; ?>
