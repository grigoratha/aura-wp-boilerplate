<?php
$menu_location = $menu_location ?? 'primary';
$menu = theme_get_menu_tree($menu_location);

if (empty($menu)) {
    return;
}
?>

<nav class="w-full">
    <ul class="menu menu-horizontal w-full bg-accent">
        <?php foreach ($menu as $item): ?>

            <?php $icon = theme_get_menu_icon($item['icon']); ?>

            <?php if (!empty($item['children'])): ?>

                <li>
                    <div class="dropdown">
                        <div tabindex="0" role="button">
                            <?php theme_render_menu_item($item, $icon); ?>
                        </div>

                        <ul tabindex="-1"
                            class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">

                            <?php foreach ($item['children'] as $child): ?>

                                <li>
                                    <?php
                                    $child_icon = theme_get_menu_icon($child['icon']);
                                    theme_render_menu_item($child, $child_icon);
                                    ?>
                                </li>

                            <?php endforeach; ?>

                        </ul>
                    </div>
                </li>

            <?php else: ?>

                <li>
                    <?php theme_render_menu_item($item, $icon); ?>
                </li>

            <?php endif; ?>

        <?php endforeach; ?>
    </ul>
</nav>