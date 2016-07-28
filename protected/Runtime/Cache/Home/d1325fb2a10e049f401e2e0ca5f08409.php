<?php if (!defined('THINK_PATH')) exit(); if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><li role="presentation">
         <a role="menuitem" tabindex="-1" href="#"><?php echo ($type); ?></a>
     </li><?php endforeach; endif; else: echo "" ;endif; ?>