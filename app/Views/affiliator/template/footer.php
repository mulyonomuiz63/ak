		</div>
		<!-- End of Main Content -->

		<!-- Footer -->
		<footer class="sticky-footer bg-white">
			<div class="container my-auto">
				<div class="copyright text-center my-auto">
					<!--<span><a target="_blank" href="https://akuntanmu.com/" style="color:#055F93;">Copyright &copy; 2019-2022, AKUNTANMU.COM</a></span>-->
				</div>
			</div>
		</footer>
		<!-- End of Footer -->

		</div>
		<!-- End of Content Wrapper -->

		</div>
		<!-- End of Page Wrapper -->

		<!-- Scroll to Top Button-->
		<a class="scroll-to-top rounded" href="#page-top">
			<i class="fas fa-angle-up"></i>
		</a>

		<!-- Logout Modal-->
		<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
						<button class="close" type="button" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
						<a class="btn btn-primary" href="login.html">Logout</a>
					</div>
				</div>
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
		</script>
		<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>

		<!-- <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script> -->


		<!-------------------------------------------------------------------------------- Function Lainnya -->
		<script type="text/javascript">
			var dateToday = new Date();
			$('#tanggal').datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: "-90:+00",
				maxDate: -1,
				dateFormat: "dd-mm-yy",
				autoclose: true,
				disableTouchKeyboard: true,
				Readonly: false,
			}).attr("readonly", "readonly");
			const numberWithCommas = (x) => {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			};

			const untitik = (i) => {
				return typeof i === 'string' ?
					i.replace(/[\$,]/g, '') * 1 :
					typeof i === 'number' ?
					i : 0;
			};

			$(document).ready(function() {
				$('.btn-copy').on("click", function() {
					var value = $('#text-copy').text();

					var $tempCopy = $("<input>");
					$("body").append($tempCopy);
					$tempCopy.val(value).select();
					document.execCommand("copy");
					$tempCopy.remove();
				})
			})
		</script>
		</body>

		</html>