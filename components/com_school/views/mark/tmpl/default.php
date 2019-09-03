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
        	<strong>sid</strong> : <?php echo $this->item->sid ?>
    	</td>
    </tr>
    <tr>
    	<td>
    		 <strong>Marathi</strong>   : <?php echo $this->item->marathi;?>
    	</td>
	</tr>
    <tr>  
    	<td>
        	  <strong>Hindi</strong> : <?php echo $this->item->hindi; ?>
    	</td>
    </tr>
    <tr>
        <td>
        	<strong>English</strong> : <?php echo $this->item->english;?>
        </td>
    </tr>
    <tr>
        <td>
        	 <strong>Image</strong>  : <?php echo $this->item->science;?>
        </td>
    </tr>
    <tr>
        <td>
        	 <strong>blood group</strong> : <?php echo $this->item->math;

        	 ?>
        </td>
    </tr>
    <tr>
         <td>
        	 <strong>percentage</strong> : <?php echo $this->item->percentage;?>
        </td>
    </tr>
     
    </tbody>
  </table>
</div>
