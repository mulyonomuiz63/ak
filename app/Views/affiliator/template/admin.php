<?php
echo $this->include('affiliator/template/header');
echo $this->include('affiliator/template/sidebar');
echo $this->include('affiliator/template/topbar');
echo $this->renderSection('content');
echo $this->include('affiliator/template/footer');
