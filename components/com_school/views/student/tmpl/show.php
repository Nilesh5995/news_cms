<div class="container" style="height: 100%; width: 80%;">
  <h2><?php echo JText::_('COM_SCHOOL_STUDENT_INFORMATION'); ?>
</h2>
<!--   <p>The .table-striped class adds zebra-stripes to a table:</p>   -->          
  <table class="table table-striped" style="height: 100%; width: 100%;">
    <thead>
      <tr>
        <th width="15%"></th>
      </tr>
    </thead>
    <tbody>
   
    <tr>
        <td>
        	<strong>Name</strong> : <?php echo $this->item->fname.' '.$this->item->mname.' '.$this->item->lname  ?>
    	</td>
    </tr>
    <tr>
    	<td>
    		 <strong>Class</strong>   : <?php echo $this->item->class;?>
    	</td>
	</tr>
    <tr>  
    	<td>
        	  <strong>Address</strong> : <?php echo $this->item->address.' '.$this->item->city.' '.$this->item->state.' '.$this->item->pincode; ?>
    	</td>
    </tr>
    <tr>
        <td>
        	<strong>Parant Mobo.</strong> : <?php echo $this->item->pmo;?>
        </td>
    </tr>
    <tr>
        <td>
        	 <strong>Image</strong> : <?php
    $src = $this->item->imageDetails['image'];
    if ($src)
    {
        $html = '<figure>
                    <img src="%s" alt="%s" >
                    <figcaption>%s</figcaption>
                </figure>';
        $alt = $this->item->imageDetails['alt'];
        $caption = $this->item->imageDetails['caption'];
        echo sprintf($html, $src, $alt, $caption);
    }
    else
    {
    ?>
    <img src="<?php echo $this->item->image;?>">
    <?php
    }
     ?>
        </td>
    </tr>
    <tr>
        <td>
        	 <strong>blood group</strong> : <?php echo $this->item->blood_group;

        	 ?>
        </td>
    </tr>
    <tr>
         <td>
        	 <strong>Dob</strong> : <?php echo $this->item->dob;?>
        </td>
    </tr>
    <tr>
          <td>
        	 <strong>Age</strong> : <?php //echo $this->item->dob;?>
        	  <?php

        	  $this->item->dob = strtotime($this->item->dob);
        	  $dob =new DateTime (DATE(('Y-m-d'),$this->item->dob));
 			  $today =new DateTime (Date(date('Y-m-d')));
 			  $interval = $today->diff($dob);
				echo $interval->format('%y years %m months and %d days')


//printf(' Your age : %d years, %d month, %d days', $diff->y, $diff->m, $diff->d);
// /printf("\n");
?>
        </td>
    </tr>
     
    </tbody>
  </table>
</div>
