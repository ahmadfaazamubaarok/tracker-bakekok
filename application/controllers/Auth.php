<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth — login, logout, ubah kata sandi, dan manajemen pengguna/grup (ion_auth 3).
 *
 * Halaman guest (login, setup admin pertama, lupa/reset kata sandi) memakai
 * layout kartu tanpa topbar. Halaman admin (daftar/buat/edit pengguna & grup,
 * ubah kata sandi) memakai layout sama dengan halaman lain via templates/.
 */
class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->helper('form');

		// pesan ion_auth dalam Bahasa Indonesia ($config['language'] tetap english
		// karena system/ hanya menyediakan english — memaksa 'indonesian' global akan
		// fatal saat library sistem memuat form_validation_lang, db_lang, dst.)
		$this->lang->load('ion_auth', 'indonesian');

		// error form_validation ditampilkan apa adanya di dalam .auth-alert / .alert
		$this->form_validation->set_error_delimiters('', '<br>');

		// terjemahan untuk aturan validasi yang dipakai form auth
		$this->form_validation->set_message('required', '{field} wajib diisi.');
		$this->form_validation->set_message('valid_email', 'Format {field} tidak valid.');
		$this->form_validation->set_message('min_length', '{field} minimal {param} karakter.');
		$this->form_validation->set_message('matches', '{field} tidak sama dengan {param}.');
		$this->form_validation->set_message('is_unique', '{field} sudah terdaftar.');
		$this->form_validation->set_message('alpha_dash', '{field} hanya boleh huruf, angka, garis bawah, dan tanda hubung.');
		$this->form_validation->set_message('alpha_numeric', '{field} hanya boleh huruf dan angka.');
	}

	/* ================================================================
	 * GUEST — halaman tanpa login
	 * ================================================================ */

	/**
	 * Login — satu-satunya pintu masuk aplikasi.
	 */
	public function login()
	{
		if ($this->ion_auth->logged_in())
		{
			redirect('/', 'refresh');
		}

		$this->form_validation->set_rules('identity', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password', 'Kata Sandi', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				redirect('/', 'refresh');
			}

			// login gagal — salah identitas/kata sandi, akun nonaktif, atau kena lockout
			$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
			redirect('auth/login', 'refresh');
		}

		$data['navbar']  = '';
		$data['error']   = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		$data['success'] = $this->session->flashdata('message');
		$this->load->view('auth/login', $data);
	}

	/**
	 * Logout — sesi dihancurkan di dalam ion_auth->logout(), jadi tidak ada
	 * flashdata setelah logout (sess_destroy menghapusnya).
	 */
	public function logout()
	{
		$this->ion_auth->logout();
		redirect('auth/login', 'refresh');
	}

	/**
	 * Setup awal — membuat akun admin pertama.
	 * Hanya aktif selama tabel users masih kosong; setelah ada pengguna,
	 * URL ini otomatis redirect ke login.
	 */
	public function setup()
	{
		if ($this->ion_auth->users()->num_rows() > 0)
		{
			redirect('auth/login', 'refresh');
		}

		$this->form_validation->set_rules('first_name', 'Nama Depan', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Nama Belakang', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password', 'Kata Sandi', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Konfirmasi Kata Sandi', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			// pastikan grup admin & members sudah ada di tabel groups
			$admin_group   = $this->config->item('admin_group', 'ion_auth');
			$default_group = $this->config->item('default_group', 'ion_auth');

			if ( ! $this->ion_auth->group($admin_group)->row())
			{
				$this->ion_auth->create_group($admin_group, 'Administrator');
			}
			if ( ! $this->ion_auth->group($default_group)->row())
			{
				$this->ion_auth->create_group($default_group, 'Anggota');
			}

			$email = strtolower($this->input->post('email'));
			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
			];

			if ($this->ion_auth->register($email, $this->input->post('password'), $email, $additional_data, [$admin_group]))
			{
				$this->session->set_flashdata('message', 'Akun admin pertama berhasil dibuat. Silakan login.');
				redirect('auth/login', 'refresh');
			}

			$data['error'] = strip_tags($this->ion_auth->errors());
		}
		else
		{
			$data['error'] = validation_errors();
		}

		$data['navbar'] = '';
		$this->load->view('auth/setup', $data);
	}

	/**
	 * Lupa kata sandi — membuat kode reset dan mengirimkannya via email.
	 * Saat use_ci_email = FALSE (config ion_auth), aplikasi meminta pemakai
	 * menghubungi admin — admin bisa set kata sandi via menu Pengguna.
	 */
	public function forgot_password()
	{
		$this->form_validation->set_rules('identity', 'Email', 'trim|required|valid_email');

		if ($this->form_validation->run() === TRUE)
		{
			$identity = $this->ion_auth->where('email', $this->input->post('identity'))->users()->row();

			if (empty($identity))
			{
				$this->session->set_flashdata('error', 'Email tidak ditemukan.');
				redirect('auth/forgot_password', 'refresh');
			}

			if ($this->ion_auth->forgotten_password($identity->email))
			{
				if ($this->config->item('use_ci_email', 'ion_auth'))
				{
					$this->session->set_flashdata('message', 'Tautan reset kata sandi telah dikirim ke email Anda.');
				}
				else
				{
					$this->session->set_flashdata('message', 'Fitur email belum aktif. Silakan hubungi administrator untuk mereset kata sandi Anda.');
				}
				redirect('auth/login', 'refresh');
			}

			$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
			redirect('auth/forgot_password', 'refresh');
		}

		$data['navbar']  = '';
		$data['error']   = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		$data['success'] = $this->session->flashdata('message');
		$this->load->view('auth/forgot_password', $data);
	}

	/**
	 * Reset kata sandi — langkah terakhir lupa kata sandi (via tautan email).
	 *
	 * @param string|null $code
	 */
	public function reset_password($code = NULL)
	{
		if ( ! $code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			$this->form_validation->set_rules('new', 'Kata Sandi Baru', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', 'Konfirmasi Kata Sandi Baru', 'required');

			if ($this->form_validation->run() === FALSE)
			{
				$data['navbar']              = '';
				$data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$data['user']                = $user;
				$data['code']                = $code;
				$data['csrf']                = $this->_get_csrf_nonce();
				$data['error']               = validation_errors();
				$this->load->view('auth/reset_password', $data);
			}
			else
			{
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{
					// permintaan mencurigakan — buang kode resetnya
					$this->ion_auth->clear_forgotten_password_code($user->email);
					show_error('Form tidak lulus pemeriksaan keamanan.');
				}

				if ($this->ion_auth->reset_password($user->email, $this->input->post('new')))
				{
					$this->session->set_flashdata('message', 'Kata sandi berhasil direset. Silakan login dengan kata sandi baru.');
					redirect('auth/login', 'refresh');
				}

				$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
				redirect('auth/reset_password/' . $code, 'refresh');
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Kode reset tidak valid atau sudah kedaluwarsa.');
			redirect('auth/forgot_password', 'refresh');
		}
	}

	/* ================================================================
	 * LOGIN — halaman setelah login
	 * ================================================================ */

	/**
	 * Ubah kata sandi sendiri (untuk semua pengguna yang sedang login).
	 */
	public function change_password()
	{
		if ( ! $this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		$this->form_validation->set_rules('old', 'Kata Sandi Lama', 'required');
		$this->form_validation->set_rules('new', 'Kata Sandi Baru', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', 'Konfirmasi Kata Sandi Baru', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$data['navbar']              = '';
			$data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$data['error']               = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$data['success']             = $this->session->flashdata('message');
			$this->load->view('auth/change_password', $data);
		}
		else
		{
			$identity = $this->session->userdata('identity');

			if ($this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new')))
			{
				// kata sandi berubah — paksa login ulang
				$this->session->set_flashdata('message', 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.');
				$this->logout();
			}

			$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
			redirect('auth/change_password', 'refresh');
		}
	}

	/* ================================================================
	 * ADMIN — manajemen pengguna & grup
	 * ================================================================ */

	/**
	 * Guard halaman admin: wajib login + grup admin.
	 */
	private function _require_admin()
	{
		if ( ! $this->ion_auth->logged_in() || ! $this->ion_auth->is_admin())
		{
			redirect('/', 'refresh');
		}
	}

	/**
	 * Daftar pengguna (halaman admin /auth).
	 */
	public function index()
	{
		$this->_require_admin();

		$data['navbar']  = 'auth';
		$data['error']   = $this->session->flashdata('error');
		$data['success'] = $this->session->flashdata('message');

		$data['users'] = $this->ion_auth->users()->result();
		foreach ($data['users'] as $k => $user)
		{
			$data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
		}

		$this->load->view('auth/index', $data);
	}

	/**
	 * Tambah pengguna baru (halaman admin).
	 */
	public function create_user()
	{
		$this->_require_admin();

		$tables = $this->config->item('tables', 'ion_auth');

		$this->form_validation->set_rules('first_name', 'Nama Depan', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Nama Belakang', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
		$this->form_validation->set_rules('password', 'Kata Sandi', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Konfirmasi Kata Sandi', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$email = strtolower($this->input->post('email'));
			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
			];

			if ($this->ion_auth->register($email, $this->input->post('password'), $email, $additional_data))
			{
				$this->session->set_flashdata('message', 'Pengguna berhasil dibuat.');
				redirect('auth', 'refresh');
			}
			$data['error'] = strip_tags($this->ion_auth->errors());
		}
		else
		{
			$data['error'] = validation_errors();
		}

		$data['navbar'] = 'auth';
		$this->load->view('auth/create_user', $data);
	}

	/**
	 * Edit pengguna — admin boleh edit siapa pun, pengguna biasa hanya dirinya.
	 *
	 * @param int|string $id
	 */
	public function edit_user($id)
	{
		if ( ! $this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && $this->ion_auth->user()->row()->id != $id))
		{
			redirect('/', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		if ($user == null)
		{
			redirect('auth', 'refresh');
		}

		$groups        = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result_array();

		$this->form_validation->set_rules('first_name', 'Nama Depan', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Nama Belakang', 'trim|required');

		if (isset($_POST) && ! empty($_POST))
		{
			// valid nonce + id harus cocok
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error('Form tidak lulus pemeriksaan keamanan.');
			}

			// kata sandi hanya divalidasi kalau diisi (opsional saat edit)
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', 'Kata Sandi', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', 'Konfirmasi Kata Sandi', 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$data = [
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
				];

				if ($this->input->post('password'))
				{
					$data['password'] = $this->input->post('password');
				}

				// hanya admin boleh mengubah grup
				if ($this->ion_auth->is_admin())
				{
					$this->ion_auth->remove_from_group('', $id);

					$groupData = $this->input->post('groups');
					if (isset($groupData) && ! empty($groupData))
					{
						foreach ($groupData as $grp)
						{
							$this->ion_auth->add_to_group($grp, $id);
						}
					}
				}

				if ($this->ion_auth->update($user->id, $data))
				{
					$this->session->set_flashdata('message', 'Perubahan pengguna berhasil disimpan.');
				}
				else
				{
					$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
				}

				// admin kembali ke daftar, pengguna biasa ke board
				if ($this->ion_auth->is_admin())
				{
					redirect('auth', 'refresh');
				}
				redirect('/', 'refresh');
			}
		}

		$data['navbar']        = 'auth';
		$data['csrf']          = $this->_get_csrf_nonce();
		$data['error']         = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		$data['user']          = $user;
		$data['groups']        = $groups;
		$data['currentGroups'] = $currentGroups;
		$data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');

		$this->load->view('auth/edit_user', $data);
	}

	/**
	 * Aktifkan pengguna (via kode aktivasi atau oleh admin).
	 *
	 * @param int         $id
	 * @param string|bool $code
	 */
	public function activate($id, $code = FALSE)
	{
		$this->_require_admin();

		if ($this->ion_auth->activate($id))
		{
			$this->session->set_flashdata('message', 'Pengguna berhasil diaktifkan.');
		}
		else
		{
			$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
		}

		redirect('auth', 'refresh');
	}

	/**
	 * Nonaktifkan pengguna (halaman konfirmasi).
	 *
	 * @param int|string|null $id
	 */
	public function deactivate($id = NULL)
	{
		$this->_require_admin();

		$id = (int) $id;

		$this->form_validation->set_rules('confirm', 'Konfirmasi', 'required');
		$this->form_validation->set_rules('id', 'ID Pengguna', 'required|alpha_numeric');

		if ($this->form_validation->run() === FALSE)
		{
			$data['navbar']   = 'auth';
			$data['csrf']     = $this->_get_csrf_nonce();
			$data['user']     = $this->ion_auth->user($id)->row();
			$data['identity'] = $this->config->item('identity', 'ion_auth');
			$this->load->view('auth/deactivate_user', $data);
		}
		else
		{
			if ($this->input->post('confirm') == 'yes')
			{
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error('Form tidak lulus pemeriksaan keamanan.');
				}

				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
				{
					if ($this->ion_auth->deactivate($id))
					{
						$this->session->set_flashdata('message', 'Pengguna berhasil dinonaktifkan.');
					}
					else
					{
						$this->session->set_flashdata('error', strip_tags($this->ion_auth->errors()));
					}
				}
			}

			redirect('auth', 'refresh');
		}
	}

	/**
	 * Tambah grup (halaman admin).
	 */
	public function create_group()
	{
		$this->_require_admin();

		$this->form_validation->set_rules('group_name', 'Nama Grup', 'trim|required|alpha_dash');

		if ($this->form_validation->run() === TRUE)
		{
			if ($this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('group_description')))
			{
				$this->session->set_flashdata('message', 'Grup berhasil dibuat.');
				redirect('auth', 'refresh');
			}
			$data['error'] = strip_tags($this->ion_auth->errors());
		}
		else
		{
			$data['error'] = validation_errors();
		}

		$data['navbar'] = 'auth';
		$this->load->view('auth/create_group', $data);
	}

	/**
	 * Edit grup (halaman admin).
	 *
	 * @param int|string $id
	 */
	public function edit_group($id)
	{
		$this->_require_admin();

		if ( ! $id || empty($id))
		{
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();
		if ($group == null)
		{
			redirect('auth', 'refresh');
		}

		$this->form_validation->set_rules('group_name', 'Nama Grup', 'trim|required|alpha_dash');

		if (isset($_POST) && ! empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				if ($this->ion_auth->update_group($id, $this->input->post('group_name'), ['description' => $this->input->post('group_description')]))
				{
					$this->session->set_flashdata('message', 'Perubahan grup berhasil disimpan.');
					redirect('auth', 'refresh');
				}
				$data['error'] = strip_tags($this->ion_auth->errors());
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}
		else
		{
			$data['error'] = '';
		}

		$data['navbar']    = 'auth';
		$data['group']     = $group;
		$data['is_admin_group'] = ($this->config->item('admin_group', 'ion_auth') === $group->name);
		$this->load->view('auth/edit_group', $data);
	}

	/* ================================================================
	 * Helper
	 * ================================================================ */

	/**
	 * Pasangan nonce CSRF — disimpan sebagai flashdata lalu dicek saat POST.
	 *
	 * @return array
	 */
	private function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	/**
	 * Cek nonce CSRF yang diposting.
	 *
	 * @return bool
	 */
	private function _valid_csrf_nonce()
	{
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		return ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue'));
	}
}
