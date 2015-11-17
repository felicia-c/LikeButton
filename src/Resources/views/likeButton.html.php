<?php
/**
 * Facebook AWD Template.
 *
 * @author AHWEBDEV (Alexandre Hermann) [hermann.alexandre@ahwebev.fr]
 */
?>
<div class="facebookAWD likeButton">
    <div class="fb-like"
         <?php if (!empty($likeButton->getHref())) { ?>
             data-href="<?php echo $likeButton->getHref(); ?>"
         <?php } if (!empty($likeButton->getLayout())) { ?>
             data-layout="<?php echo $likeButton->getLayout(); ?>"
         <?php } if (!empty($likeButton->getAction())) { ?>
             data-action="<?php echo $likeButton->getAction(); ?>"
         <?php } if (!empty($likeButton->getShowFaces())) { ?>
             data-show-faces="<?php echo $likeButton->getShowFaces(); ?>"
         <?php } if (!empty($likeButton->getShare())) { ?>
             data-share="<?php echo $likeButton->getShare(); ?>"
         <?php } if (!empty($likeButton->getKidDirectedSite())) { ?>
             data-kid-directed-site="<?php echo $likeButton->getKidDirectedSite(); ?>"
         <?php }if (!empty($likeButton->getColorscheme())) { ?>
             data-colorscheme="<?php echo $likeButton->getColorscheme(); ?>"
         <?php } if (!empty($likeButton->getRef())) { ?>
             data-ref="<?php echo $likeButton->getRef(); ?>"
         <?php } if (!empty($likeButton->getWidth())) { ?>
             data-width="<?php echo $likeButton->getWidth(); ?>"
         <?php } ?>>
    </div>
</div>
