<div class="container2">
  <div class="info">
    <h2><?php __('Pending Submissions');?></h2>
    <div></div>
  </div>
</div>

<div class="container2">
  <div class="info">
    <h2><?php __('Collectibles');?></h2>
    <div>There are <?php echo $collectibleSubCount ?> collectibles that need to be approved.</div>
    <?php 
      if($collectibleSubCount > 0)
      {
         echo "<div> {$html->link('Approve', array('controller' => 'adminCollectibles', 'action' => 'pending'))} </div>";
      }   
     ?>
  </div>
</div>


