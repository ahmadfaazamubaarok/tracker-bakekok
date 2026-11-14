<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * App helper — utilitas tampilan: label prioritas, format tanggal Indonesia,
 * dan cek overdue (BR-09: deadline < hari ini dan status != done).
 */

if ( ! function_exists('priority_label'))
{
	/**
	 * Label prioritas ID dari value EN (BR-07).
	 *
	 * @param	string	$priority	low|medium|high
	 * @return	string
	 */
	function priority_label($priority)
	{
		$labels = array(
			'low' => 'Rendah',
			'medium' => 'Sedang',
			'high' => 'Tinggi',
		);
		return isset($labels[$priority]) ? $labels[$priority] : $priority;
	}
}

if ( ! function_exists('format_tanggal'))
{
	/**
	 * Format tanggal Indonesia "31 Des 2026".
	 *
	 * @param	string	$date	Y-m-d atau Y-m-d H:i:s
	 * @param	bool	$with_year	tampilkan tahun
	 * @return	string
	 */
	function format_tanggal($date, $with_year = TRUE)
	{
		if (trim((string) $date) == '' OR $date == '0000-00-00')
		{
			return '-';
		}

		$bulan = array(
			1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
			5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
			9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
		);

		$ts = strtotime($date);
		if ($ts === FALSE)
		{
			return '-';
		}

		$hasil = date('j', $ts).' '.$bulan[(int) date('n', $ts)];
		if ($with_year)
		{
			$hasil .= ' '.date('Y', $ts);
		}
		return $hasil;
	}
}

if ( ! function_exists('is_overdue'))
{
	/**
	 * Overdue = deadline < hari ini dan status != done (BR-09).
	 * Pembanding string Y-m-d — aman dari isu timezone (lihat docs/05 §3).
	 *
	 * @param	string	$deadline	Y-m-d
	 * @param	string	$status		pending|wip|done
	 * @return	bool
	 */
	function is_overdue($deadline, $status)
	{
		if (trim((string) $deadline) == '')
		{
			return FALSE;
		}
		return (date('Y-m-d') > date('Y-m-d', strtotime($deadline))) && $status != 'done';
	}
}

if ( ! function_exists('require_login'))
{
	/**
	 * Guard login — panggil di constructor controller yang wajib login.
	 *
	 * Belum login: halaman dialihkan ke auth/login; request AJAX (progress_daftar,
	 * project_addsave, dst.) dibalas JSON 401 agar handler .fail() yang menampilkan
	 * pesan "Sesi berakhir", bukan HTML login yang ditumpuk ke modal.
	 */
	function require_login()
	{
		$ci = get_instance();

		if ($ci->ion_auth->logged_in() || ENVIRONMENT === 'development')
		{
			return;
		}

		if ($ci->input->is_ajax_request())
		{
			$ci->output
				->set_status_header(401)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode(['status' => 'gagal', 'pesan' => 'Sesi berakhir. Silakan login kembali.']))
				->_display();
			exit;
		}

		redirect('auth/login', 'refresh');
	}
}
