<div class="container2">
  <div class="info">
    <h2><?php __('Who has it?');?></h2>
  </div>
  
  <div class="users view">
    <ul>
    <?php  
    foreach ($usersWho as $user):
    ?>
      <li>
        <?php echo $user['User']['username']; ?>  
      </li>  
    <?php endforeach; ?>
    </ul>
  </div>
</div>