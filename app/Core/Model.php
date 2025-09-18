
<?php
class Model {
    protected $db; protected $table;
    public function __construct() { $this->db = (new Database())->getConnection(); }
    public function findAll() { $stmt = $this->db->query("SELECT * FROM {$this->table}"); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
    public function find($id) { $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id LIMIT 1"); $stmt->execute([':id'=>$id]); return $stmt->fetch(PDO::FETCH_ASSOC); }
    public function create($data) { /* build INSERT ... */ }
    public function update($id, $data) { /* build UPDATE ... */ }
    public function delete($id) { /* build DELETE ... */ }
}