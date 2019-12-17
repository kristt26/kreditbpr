<?php

class Periode_Model extends CI_Model
{
    protected $periodeTable = 'periode';
    public function Insert($Data)
    {
        $this->db->insert($this->KriteriaTable, $Data);
        return $this->db->insert_id();
    }
    public function Update($idperiode, $data)
    {
        $this->db->where("idperiode", $idperiode);
        $result = $this->db->update($this->periodeTable, $data);
        return $result;
    }
    public function Select()
    {
        $result = $this->db ->get($this->periodeTable);
        $periodes = $result->result_object();
        foreach ($periodes as $keyperiode => $periode) {
            // Get Debitur
            $result = $this->db->get("debitur");
            $debitur = $result->result_object();

            // Get Kriteria
            $result = $this->db->get("kriteria");
            $Kriteria = $result->result_object();

            foreach ($debitur as $key => $value) {
                $value->Kriteria = $Kriteria;
                $num = 0;
                foreach ($value->Kriteria as $key1 => $value1) {
                    $result = $this->db->query("
                        SELECT
                            `subkriteria`.*,
                            `datakriteria`.`nilai`
                        FROM
                            `subkriteria`
                            LEFT JOIN `datakriteria` ON `subkriteria`.`idSubKriteria` =
                            `datakriteria`.`idSubKriteria`
                            LEFT JOIN `periode` ON `datakriteria`.`idperiode` = `periode`.`idperiode`
                        WHERE 
                            `subkriteria`.idkriteria = '$value1->idkriteria' AND
                            datakriteria.iddebitur = '$value->iddebitur' AND
                            periode.idperiode = '$periode->idperiode'
                ");
                    if ($result->num_rows() > 0) {
                        $value1->subKriteria = $result->result_object();
                    } else {
                        $num += 1;
                    }
                }
                if ($num > 0) {
                    unset($debitur[$key]);
                }
            }
            $periode->debitur = $debitur;
        }
        return $periode;
    }
    public function Delete($idKriteria)
    {
        $this->db->where("idKriteria", $idKriteria);
        $result = $this->db->delete($this->KriteriaTable);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}