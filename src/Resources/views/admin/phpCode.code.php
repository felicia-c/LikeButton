<?php
/**
 * Facebook AWD Template
 *
 * @package FacebookAWD
 * @subpackage FacebookAWDLikeButton
 * @author AHWEBDEV (Alexandre Hermann) [hermann.alexandre@ahwebev.fr]
 */
?>
<h4 class="text-primary">Using PHP</h4>
<pre class="prettyprint lang-php">
<?php echo htmlentities('<?php') . "\n"; ?>

/* Using the shortcode in php */
echo do_shortcode('<?php echo $shortcode; ?>');

/* OR Using the php object */
<?php echo $likebuttonCode . "\n"; ?>

<?php echo htmlentities('?>'); ?>
</pre>