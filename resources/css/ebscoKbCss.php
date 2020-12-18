<style>

    .ebsco-layout .container {
        margin: 1em 15px;
    }
    .ebsco-layout .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        flex-flow: wrap;
    }
    <?php for($i=1; $i<=12; $i++): ?>
    .ebsco-layout .col-<?php echo $i; ?> {
        width: <?php echo $i / 12 * 100; ?>%;
    }
    <?php endfor; ?>

    <?php for($i=1; $i<=5; $i++): ?>
    <?php foreach(['t' => '-top', 'b' => '-bottom', 'l' => '-left', 'r' => '-right', 'a' => ''] as $selector => $ord): ?>
    .ebsco-layout .m<?php echo $selector; ?>-<?php echo $i; ?> {
        margin<?php echo $ord; ?>: <?php echo $i; ?>em;
    }
    .ebsco-layout .p<?php echo $selector; ?>-<?php echo $i; ?> {
        padding<?php echo $ord; ?>: <?php echo $i; ?>em;
    }
    <?php endforeach; ?>

    <?php endfor; ?>

    .ebsco-layout dt {
        font-weight: 700;
        margin-top: 5px;
    }

    .ebsco-layout .card-body {
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        padding: 5px;
    }

    .ebsco-layout .card {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;
    }

    .ebsco-layout .card-header {
        padding: .75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    .ebsco-layout div[id^="providerOption-help"] {
        display: none;
    }

    .ebsco-layout #importError {
        border: 1px solid;
        padding: 1em;
        margin-top: 1em;
        background-color: #f5e7e4;
        border-radius: .25rem;
        display: none;
    }

    .ebsco-layout #importOverlay {
        position: absolute;
        background: #1b76a9;
        width: 100%;
        height: 670px;
        top: 0;
        left: 0;
        color: #fff;
        display: none;
    }

    .ebsco-layout #importingMessage a{
        color: #fff;
    }

    .ebsco-layout #importingMessage .text-success {
        color: #62bc22;
    }

    .ebsco-layout .progress {
        display: -ms-flexbox;
        display: flex;
        height: 1rem;
        overflow: hidden;
        font-size: .75rem;
        background-color: #e9ecef;
        border-radius: .25rem;
    }

    .ebsco-layout .progress-bar {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: center;
        justify-content: center;
        color: #fff;
        background-color: #4B7717;
    }

    .ebsco-layout #importLog {
        color: #666;
        max-height: 430px;
        overflow-y: scroll;
    }

    /* Dropdown */

    .ebsco-select-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .ebsco-select-dropdown .dd-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        text-align: left;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .ebsco-select-dropdown .dd-content.show {
        display: block;
    }



    .ebsco-select-dropdown .dd-content a {
        color: black;
        padding: 7px 16px;
        text-decoration: none;
        display: block;
    }

    .ebsco-select-dropdown .dd-content a:hover {
        background-color: #ddd;
    }

    .ebsco-layout .packageOption {
        display: none;
    }

    .ebsco-layout .selectedPackage {
        display: block;
    }


</style>