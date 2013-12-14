<?php 
class ControllerAccountRecurring extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->language->load('account/recurring');

		$this->load->model('account/recurring');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/recurring', $url, 'SSL'),
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['column_created'] = $this->language->get('column_created');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_product'] = $this->language->get('column_product');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_profile_id'] = $this->language->get('column_profile_id');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_continue'] = $this->language->get('button_continue');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['orders'] = array();

		$recurring_total = $this->model_account_recurring->getTotalRecurring();

		$results = $this->model_account_recurring->getAllProfiles(($page - 1) * 10, 10);

		$data['profiles'] = array();

		if($results){
			foreach ($results as $result) {
				$data['profiles'][] = array(
					'id'                    => $result['order_recurring_id'],
					'name'                  => $result['product_name'],
					'status'                => $result['status'],
					'created'               => date($this->language->get('date_format_short'), strtotime($result['created'])),
					'href'                  => $this->url->link('account/recurring/info','recurring_id='.$result['order_recurring_id'],'SSL'),
				);
			}
		}

		$data['status_types'] = array(
			1 => $this->language->get('text_status_inactive'),
			2 => $this->language->get('text_status_active'),
			3 => $this->language->get('text_status_suspended'),
			4 => $this->language->get('text_status_cancelled'),
			5 => $this->language->get('text_status_expired'),
			6 => $this->language->get('text_status_pending'),
		);

		$pagination = new Pagination();
		$pagination->total = $recurring_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/recurring', 'page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['continue'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/recurring_list.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/recurring_list.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/recurring_list.tpl', $data));
		}
	}

	public function info() {
		$this->load->model('account/recurring');
		$this->load->language('account/recurring');

		if (isset($this->request->get['recurring_id'])) {
			$recurring_id = $this->request->get['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/recurring/info', 'recurring_id=' . $recurring_id, 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if(isset($this->session->data['error'])){
			$data['error'] = $this->session->data['error'];
			unset($this->session->data['error']);
		}else{
			$data['error'] = '';
		}

		if(isset($this->session->data['success'])){
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}else{
			$data['success'] = '';
		}

		$profile = $this->model_account_recurring->getProfile($this->request->get['recurring_id']);

		$data['status_types'] = array(
			1 => $this->language->get('text_status_inactive'),
			2 => $this->language->get('text_status_active'),
			3 => $this->language->get('text_status_suspended'),
			4 => $this->language->get('text_status_cancelled'),
			5 => $this->language->get('text_status_expired'),
			6 => $this->language->get('text_status_pending'),
		);

		$data['transaction_types'] = array(
			0 => $this->language->get('text_transaction_created'),
			1 => $this->language->get('text_transaction_payment'),
			2 => $this->language->get('text_transaction_outstanding_payment'),
			3 => $this->language->get('text_transaction_skipped'),
			4 => $this->language->get('text_transaction_failed'),
			5 => $this->language->get('text_transaction_cancelled'),
			6 => $this->language->get('text_transaction_suspended'),
			7 => $this->language->get('text_transaction_suspended_failed'),
			8 => $this->language->get('text_transaction_outstanding_failed'),
			9 => $this->language->get('text_transaction_expired'),
		);

		if ($profile) {
			$profile['transactions'] = $this->model_account_recurring->getProfileTransactions($this->request->get['recurring_id']);

			$profile['created'] = date($this->language->get('date_format_short'), strtotime($profile['created']));
			$profile['product_link'] = $this->url->link('product/product', 'product_id='.$profile['product_id'], 'SSL');
			$profile['order_link'] = $this->url->link('account/order/info', 'order_id='.$profile['order_id'], 'SSL');

			$data['action_buttons'] = $this->load->controller('payment/' . $profile['payment_code'].'/getActions', '');

			$this->document->setTitle($this->language->get('text_recurring'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
			);

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/recurring', $url, 'SSL'),
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_recurring'),
				'href'      => $this->url->link('account/recurring/info', 'recurring_id=' . $this->request->get['recurring_id'] . $url, 'SSL'),
			);

			$data['heading_title'] = $this->language->get('text_recurring');

			$data['column_created'] = $this->language->get('column_created');
			$data['column_type'] = $this->language->get('column_type');
			$data['column_amount'] = $this->language->get('column_amount');

			$data['text_recurring_id'] = $this->language->get('text_recurring_id');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_empty_transactions'] = $this->language->get('text_empty_transactions');
			$data['text_payment_method'] = $this->language->get('text_payment_method');
			$data['text_recurring_detail'] = $this->language->get('text_recurring_detail');
			$data['text_status'] = $this->language->get('text_status');
			$data['text_ref'] = $this->language->get('text_ref');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_order'] = $this->language->get('text_order');
			$data['text_quantity'] = $this->language->get('text_quantity');
			$data['text_transactions'] = $this->language->get('text_transactions');
			$data['text_recurring_description'] = $this->language->get('text_recurring_description');

			$data['button_return'] = $this->language->get('button_return');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['continue'] = $this->url->link('account/recurring', '', 'SSL');
			$data['profile'] = $profile;

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/recurring_info.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/recurring_info.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/account/recurring_info.tpl', $data));
			}
		} else {
			$this->response->redirect($this->url->link('account/recurring', '', 'SSL'));
		}
	}
}