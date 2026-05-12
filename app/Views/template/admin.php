<?php
echo $this->include('template/header');
echo $this->include('template/sidebar');
echo $this->include('template/topbar');
echo $this->renderSection('content');
echo $this->include('template/footer');
