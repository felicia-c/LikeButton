<?php
/**
 * View Admin template
 * @author AHWEBDEV (Alexandre Hermann) [hermann.alexandre@ahwebev.fr]
 */
?>
<div class="facebookAWD likeButton">
    <div class="fb-like" 
         data-href="<?php echo $likeButton->getHref(); ?>" 
         data-layout="<?php echo $likeButton->getLayout(); ?>" 
         data-action="<?php echo $likeButton->getAction(); ?>" 
         data-show-faces="<?php echo $likeButton->getShowFaces(); ?>"
         data-share="<?php echo $likeButton->getShare(); ?>"
         data-kid-directed-site="<?php echo $likeButton->getKidDirectedSite(); ?>"
         data-colorscheme="<?php echo $likeButton->getColorscheme(); ?>"
         data-ref="<?php echo $likeButton->getRef(); ?>"
         data-width="<?php echo $likeButton->getWidth(); ?>">
    </div>
</div>

