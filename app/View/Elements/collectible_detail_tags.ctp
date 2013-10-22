
<?php
if (isset($collectibleCore['CollectiblesTag']) && !empty($collectibleCore['CollectiblesTag'])) {
    foreach ($collectibleCore['CollectiblesTag'] as $tag) {
        echo '';
        echo '<a class="btn btn-default" href="/collectibles/search/?t=' . $tag['Tag']['id'] . '"';
        echo '>' . $tag['Tag']['tag'] . '</a>';
        echo '';
    }
}
?>