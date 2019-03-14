<div class="row m-0">
			<div class="col-12 col-lg-6">
				<h4>Congratulations, well done.</h4>
				<h4 class="m-b-30">No further action is required from you.</h4>
				<p>Your Amazon data (orders, returns, FC transfers) since 1.1.2017 is now scheduled to be downloaded within next hour.</p>
			</div>
			<div class="col-12 col-lg-6 border border-secondary rounded p-b-10 m-b-20">
				<h5>Import data</h5>
				<div class="col-12 m-b-20 text-muted">
					If you wish to see your data of last 20 days right now, you can download them manually using blue buttons. <br>
				</div>
				<div class="row">
					<label class="col-6 col-form-label" for="mws_d_from">Date from: </label>
					<label class="col-6 col-form-label" for="mws_d_to">to: </label>
				</div>
				<div class="form-group row m-b-20">
					<div class="col-6">
						<input type="text" class="form-control" id="mws_d_from" name="d_from" value="2018-09-18" readonly="">
					</div>														
					<div class="col-6">
						<input type="text" class="form-control" id="mws_d_to" name="d_to" value="2018-10-08" readonly="">
					</div>
				</div>
				<div class="row form-group" id="mws_manual_buttons">
					<div class="col-12 col-sm-6 m-b-10">
						<button type="button" class="btn btn-primary btn-block mws_manual" process="fba_orders">Get FBA Orders</button>
					</div>
					<div class="col-12 col-sm-6 m-b-10">
						<button type="button" class="btn btn-primary btn-block mws_manual" process="fba_returns">Get FBA Returns</button>
					</div>
					<div class="col-12 col-sm-6 m-b-10">
						<button type="button" class="btn btn-primary btn-block mws_manual" process="fbm_orders">Get FBM Orders</button>
					</div>
					<div class="col-12 col-sm-6 m-b-10">	
						<button type="button" class="btn btn-primary btn-block mws_manual" process="fc_transfers">Get FC transfers</button>
					</div>
				</div>
				<div id="mws_log" class="border border-success rounded" style="display: none; padding: 10px" role="alert">
				</div>
			</div>
		</div>