<div class="col-md-12">
<div class="row">
<h1 class="page-header">
   All Orders
</h1>
</div>
<h4 class= "bg-success"><?php display_message(); ?></h4>
<div class="row">
<table class="table table-hover">
    <thead>

      <tr>
           <th>#</th>
           <th>Kiekis</th>
           <th>Kodas</th>
           <th>Valiuta</th>
           <th>Būklė</th>
      </tr>
    </thead>
    <tbody>
        <?php display_orders(); ?>
    </tbody>
</table>
</div>