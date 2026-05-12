		</div>
		<!-- End of Main Content -->

		<!-- Footer -->
		<!--<footer class="sticky-footer bg-white">-->
		<!--	<div class="container my-auto">-->
		<!--		<div class="copyright text-center my-auto">-->
					<!--<span><a target="_blank" href="https://akuntanmu.com/" style="color:#055F93;">Copyright &copy; 2019-2022, AKUNTANMU.COM</a></span>-->
		<!--		</div>-->
		<!--	</div>-->
		<!--</footer>-->
		<!-- End of Footer -->

		</div>
		<!-- End of Content Wrapper -->

		</div>
		<!-- End of Page Wrapper -->

		<!-- Scroll to Top Button-->
		<a class="scroll-to-top rounded" href="#page-top">
			<i class="fas fa-angle-up"></i>
		</a>

		<!-- Chat Floating Button -->
        <div id="chatButton">
            <a href="javascript:void(0)" data-url="Halo,%20saya%20ingin%20bertanya..." 
               class="whatsapp-button d-flex align-items-center justify-content-center">
                <?= img_lazy('uploads/icon/call.png',"loading", ['class' => '', "width" => "50px", "height" => "50px"]) ?>
            </a>
        </div>
        
        <!-- Chat Box -->
        <div class="chat-box shadow-lg animated" id="chatBox">
            <div class="chat-header d-flex justify-content-between align-items-center px-3 py-2">
                <div class="d-flex align-items-center">
                    <img src="<?= base_url('uploads/icon/call.png') ?>" class="rounded-circle mr-2" width="40">
                    <div>
                        <strong>Akuntanmu</strong><br>
                        <small class="text-success">Online</small>
                    </div>
                </div>
        
                <button class="btn btn-sm btn-light rounded-circle" id="closeChat">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <div class="chat-body px-3 py-3">
                <p><strong>Halo!</strong> Saya Akuntanmu 👋<br>Ada yang bisa saya bantu?</p>
        
                <hr>
        
                <div class="d-flex flex-column">
                    <a href="javascript:void(0)" data-url="Halo Akuntanmu,%20saya%20ingin%20bertanya..." target="_blank" 
                       class="btn btn-success btn-block mb-2 myFunctionWACs">
                       💬 Customer Service
                    </a>
        
                    <a href="javascript:void(0)" data-url="Halo Akuntanmu,%20saya%20ingin%20bertanya..." target="_blank" 
                       class="btn btn-primary btn-block myFunctionWATs">
                       🛠 Technical Support & Konfirmasi Langganan 
                    </a>
                </div>
            </div>
        </div>
		
	    <!--untuk loading-->
	    <div id="loading-overlay">
          <div class="text-center">
            <div class="spinner-border text-light mb-3" role="status">
              <span class="sr-only">Loading...</span>
            </div>
            <div class="text-light">Memuat data, harap tunggu...</div>
          </div>
        </div>

		<!-- Bootstrap core JavaScript-->
		<script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

		<!-- Core plugin JavaScript-->
		<script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

		<!-- Custom scripts for all pages-->
		<script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.js')) ?>"></script>

		<!-- Page level plugins -->
		<!-- <script src="<?php echo (base_url('assets/sb-admin-2/vendor/chart.js/Chart.min.js')) ?>"></script> -->

		<!-- Page level custom scripts -->
		<!-- <script src="<?php echo (base_url('assets/sb-admin-2/js/demo/chart-area-demo.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/js/demo/chart-pie-demo.js')) ?>"></script> -->

		<!-- datatables -->
		<script src="<?php echo (base_url('assets/datatables/js/jquery.dataTables.min.js')) ?>"></script>


		<script type="text/javascript" src="<?php echo base_url('assets/bootbox/bootbox.js'); ?>"></script>


		<!-- jquery-confirm  -->
		<script src="<?php echo (base_url('assets/jquery-confirm/js/jquery-confirm.min.js')) ?>"></script>

		<!-- jquery-mask -->
		<script type="text/javascript" src="<?php echo base_url('assets/jquery_mask/jquery.mask.js') ?>"></script>

		<!-- Bootstrap validator -->
		<script src="<?php echo (base_url('assets/bootstrap-validator/js/bootstrapValidator.js')) ?>"></script>

		<!-- jquery-ui -->
		<script type="text/javascript">
			$(".tooltips").tooltip();
			$('body').tooltip({
                selector: '[title]',
                container: 'body',
                boundary: 'window'
            });

		</script>
		<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>

		<!-- <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script> -->


		<!-------------------------------------------------------------------------------- Function Lainnya -->
		<script>
			const numberWithCommas = (x) => {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}

			const untitik = (i) => {
				return typeof i === 'string' ?
					i.replace(/[\$,]/g, '') * 1 :
					typeof i === 'number' ?
					i : 0;
			};

			function number_format(number, decimals, dec_point, thousands_sep) {
				// Strip all characters but numerical ones.
				number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
				var n = !isFinite(+number) ? 0 : +number,
					prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
					sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
					dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
					s = '',
					toFixedFix = function(n, prec) {
						var k = Math.pow(10, prec);
						return '' + Math.round(n * k) / k;
					};
				// Fix for IE parseFloat(0.55).toFixed(0) = 0;
				s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
				if (s[0].length > 3) {
					s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
				}
				if ((s[1] || '').length < prec) {
					s[1] = s[1] || '';
					s[1] += new Array(prec - s[1].length + 1).join('0');
				}
				return s.join(dec);
			}

			$('#tgljurnal').datepicker({
				maxDate: new Date(),
				changeMonth: true,
				changeYear: true,
				yearRange: "-90:+00",
				// maxDate: -1,
				dateFormat: "dd-mm-yy",
				autoclose: true,
				disableTouchKeyboard: true,
				Readonly: false,
			}).on('change', function(e) {
				// Revalidate the date field
				$('#form').bootstrapValidator('revalidateField', 'tgljurnal');
			});

			window.setTimeout(function() {
				$(".alert").fadeTo(500, 0).slideUp(500, function() {
					$(this).remove();
				});
			}, 10000);
			
			
			document.querySelectorAll("a.collapse-item").forEach(function(link) {
                link.addEventListener("click", function(event) {
                    document.getElementById("loading-overlay").style.display = "flex";
                });
            });
            
            document.forms[0].addEventListener("submit", function(e) {
                document.getElementById("loading-overlay").style.display = "flex";
            });
            
            document.querySelectorAll("#searchLaporan").forEach(function(link) {
                link.addEventListener("click", function(event) {
                    document.getElementById("loading-overlay").style.display = "flex";
                });
            });

            // Hilangkan loading setelah halaman selesai dimuat
            window.addEventListener("load", function(){
                document.getElementById("loading-overlay").style.display = "none";
            });
		</script>
		<script>
            document.addEventListener("DOMContentLoaded", function(){
              const sidebar = document.getElementById("accordionSidebar");
              const content = document.getElementById("scroll");
            
              let scrollTop = 0;
              const step = 40; // seberapa jauh geser tiap scroll
            
              sidebar.addEventListener("wheel", function(e){
                e.preventDefault();
            
                const maxScroll = content.scrollHeight - sidebar.clientHeight;
                scrollTop += e.deltaY > 0 ? step : -step;
            
                if(scrollTop < 0) scrollTop = 0;
                if(scrollTop > maxScroll) scrollTop = maxScroll;
            
                // geser pakai translateY
                content.style.transform = `translateY(-${scrollTop}px)`;
              });
            });
        </script>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
          let lazyImages = document.querySelectorAll("img.lazy");
        
          if ("IntersectionObserver" in window) {
            // ✅ Browser support IntersectionObserver
            let observer = new IntersectionObserver((entries, obs) => {
              entries.forEach(entry => {
                if (entry.isIntersecting) {
                  let img = entry.target;
                  img.src = img.dataset.src;
                  img.removeAttribute("data-src");
                  img.classList.remove("lazy");
                  obs.unobserve(img);
                }
              });
            });
        
            lazyImages.forEach(img => observer.observe(img));
        
          } else {
            // ⚠️ Fallback kalau browser tidak support
            lazyImages.forEach(img => {
              img.src = img.dataset.src;
              img.removeAttribute("data-src");
              img.setAttribute("loading", "lazy");
              img.classList.remove("lazy");
            });
          }
        });
        </script>
        <script>
            const chatButton = document.getElementById("chatButton");
            const chatBox = document.getElementById("chatBox");
            const closeChat = document.getElementById("closeChat");
        
            chatButton.addEventListener("click", () => {
              chatBox.style.display = "flex";
              chatButton.style.display = "none";
            });
        
            closeChat.addEventListener("click", () => {
              chatBox.style.display = "none";
              chatButton.style.display = "flex";
            });
            
            $('.myFunctionWACs').click(function() {
                var slugWa = $(this).data('url');
                window.location = "https://api.whatsapp.com/send?phone=6282180744966&text="+slugWa;
            });
            
            $('.myFunctionWATs').click(function() {
                var slugWa = $(this).data('url');
                window.location = "https://api.whatsapp.com/send?phone=6281532423436&text="+slugWa;
            });
            $(document).on('click', '.myFunctionKonfirmasi', function (e) {
                e.preventDefault();
            
                var slugWa = $(this).data('url');
                window.location.href =
                    "https://api.whatsapp.com/send?phone=6281532423436&text=" + slugWa;
            });
        </script>

		</body>

		</html>