
<div class="modal-body">
    <div class="card">
        <div class="front">
            <img src="<?php echo root_url();?>/assets/itsolution24/img/card/card.png" alt="" class="card_img">
            <div class="card-content white-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="353px" height="206px" xmlns:xlink="http://www.w3.org/1999/xlink">
                <text x="5" y="20" style="font-size:16;fill:#FFF;">
                    <?php echo $language->get('text_gift_card'); ?>                           
                </text>
                <text x="175" y="20" style="font-size:16;fill:#FFF;">
                    <?php echo $giftcard['card_no'];?>                           
                </text>
                <text x="5" y="75" style="font-size:36;fill:#FFF;">
                    <?php echo get_currency_code();?> <?php echo number_format($giftcard['balance'], 2);?>                           
                </text>
                <text x="5" y="98" style="font-size:14;fill:#FFF;">
                </text>
                <text x="5" y="115" style="font-size:14;fill:#FFF;">
                    <?php echo $language->get('text_expiry'); ?>: <?php echo $giftcard['expiry'];?>                           
                </text>
                </svg>
                <div class="giftcard-barcode">
                    <div class="text-center">
                         <?php
                          $generator = barcode_generator();
                          $symbology = barcode_symbology($generator, 'code_39');?>
                          <img class="bcimg" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($giftcard['card_no'], $symbology, 1)); ?>" height="20">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="back">
            <img src="<?php echo root_url();?>/assets/itsolution24/img/card/card2.png" alt="" class="card_img">
            <div class="card-content">
                <div class="middle">
                    <?php if ($store->get('logo')): ?>
                      <img src="<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/<?php echo $store->get('logo'); ?>">
                    <?php else: ?>
                      <img src="<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/nologo.png">
                    <?php endif; ?>                      
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="text-center">
        <button type="button" class="btn btn-primary no-print" onclick="window.print();">
            <span class="fa fa-fw fa-print"></span> <?php echo $language->get('button_print'); ?>
        </button>
    </div>
</div>