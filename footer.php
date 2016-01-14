<?php
if (!isset($started_at)) {
    $started_at = microtime(true);
}
?>
<footer class="footer">
    <div class="row text-center">
        <p><?="Page generated in " . round((microtime(true) - $started_at), 4) . " seconds"?></p>
    </div>
</footer>