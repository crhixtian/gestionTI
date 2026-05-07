<?php
class DetalleRequerimientoModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	// Lista todos los detalles de un requerimiento con información de tecnología
	public function listarDetallesPorRequerimiento($idRequerimiento)
	{
		$sql = "
			SELECT
				d.Id,
				d.IdRequerimiento,
				d.IdCatalogoTecnologico,
				-- Sin homologar
				ISNULL(ct.Codigo, '') AS CodigoTecnologia,
				d.CodigoSiga,
				d.Clasificador,
				d.DescripcionDetallada,
				d.Cantidad,
				d.UnidadMedida
			FROM adquisiciones.DetalleRequerimiento d
			LEFT JOIN adquisiciones.CatalogoTecnologico ct ON ct.Id = d.IdCatalogoTecnologico
			WHERE d.IdRequerimiento = ?
			ORDER BY d.Id
		";

		$stmt = sqlsrv_query($this->db, $sql, [$idRequerimiento]);
		if ($stmt === false) {
			return [];
		}

		$data = [];
		while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			$data[] = $row;
		}

		return $data;
	}

	// Guarda un nuevo detalle de requerimiento retornando su ID generado
	public function guardarDetalle($datos)
	{
		$sql = "INSERT INTO adquisiciones.DetalleRequerimiento 
		        (IdRequerimiento, IdCatalogoTecnologico, CodigoSiga, Clasificador, DescripcionDetallada, Cantidad, UnidadMedida, idUsuarioRegistro) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?); 
		        SELECT SCOPE_IDENTITY() AS Id;";
		
		$params = [
			$datos['IdRequerimiento'],
			$datos['IdCatalogoTecnologico'],
			$datos['CodigoSiga'],
			$datos['Clasificador'] ?? null,
			$datos['DescripcionDetallada'],
			$datos['Cantidad'],
			$datos['UnidadMedida'],
			$datos['idUsuarioRegistro'] ?? null
		];

		$stmt = sqlsrv_query($this->db, $sql, $params);
		
		if ($stmt === false) {
			return false;
		}

		sqlsrv_next_result($stmt);
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		
		return $row ? $row['Id'] : false;
	}

	// Actualiza los datos de un detalle de requerimiento existente
	public function actualizarDetalle($id, $datos)
	{
		$sql = "UPDATE adquisiciones.DetalleRequerimiento 
		        SET IdCatalogoTecnologico = ?,
		            CodigoSiga = ?, 
		            Clasificador = ?,
		            DescripcionDetallada = ?, 
		            Cantidad = ?, 
		            UnidadMedida = ?,
		            idUsuarioModifica = ?,
		            FechaModifica = GETDATE()
		        WHERE Id = ?";
		
		$params = [
			$datos['IdCatalogoTecnologico'],
			$datos['CodigoSiga'],
			$datos['Clasificador'] ?? null,
			$datos['DescripcionDetallada'],
			$datos['Cantidad'],
			$datos['UnidadMedida'],
			$datos['idUsuarioModifica'] ?? null,
			$id
		];

		$stmt = sqlsrv_query($this->db, $sql, $params);
		return $stmt !== false;
	}

	// Elimina un detalle de requerimiento por ID
	public function eliminarDetalle($id)
	{
		$sql = "DELETE FROM adquisiciones.DetalleRequerimiento WHERE Id = ?";
		$stmt = sqlsrv_query($this->db, $sql, [$id]);
		return $stmt !== false;
	}

	// Lista todas las tecnologías activas del catálogo como opciones de selección
	public function listarOpcionesCatalogoTecnologico()
	{
		$sql = "SELECT Id, Codigo, NombreGenerico FROM adquisiciones.CatalogoTecnologico WHERE Activo = 1";
		$stmt = sqlsrv_query($this->db, $sql);
		if ($stmt === false) {
			return [];
		}

		$data = [];
		while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			$data[] = [
				'Id' => (int) $row['Id'],
				'Codigo' => (string) $row['Codigo'],
				'NombreGenerico' => (string) $row['NombreGenerico'],
			];
		}

		usort($data, static function ($a, $b) {
			$comparacionCodigo = strnatcasecmp($a['Codigo'], $b['Codigo']);
			if ($comparacionCodigo !== 0) {
				return $comparacionCodigo;
			}

			return strcasecmp($a['NombreGenerico'], $b['NombreGenerico']);
		});

		return $data;
	}
}
