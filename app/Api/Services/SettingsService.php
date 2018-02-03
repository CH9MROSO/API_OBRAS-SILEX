<?php
namespace Api\Services;

/**
 * Settings Service.
 */
class SettingsService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'settings';


    /**
     * Get all settings.
     *
     * @param $user_id
     * @return $entity or false.
     */ 
    public function getSettings($user_id = 0) {

        $qb = $this->db->createQueryBuilder();
        $search_params = [];
        if ($user_id) {
            $qb->select('s.alias AS alias', 'us.setting_value AS setting_value', 's.setting_value AS default_value');
            $qb->from('settings', 's');
            $qb->leftJoin(
                's',
                'users_settings',
                'us',
                's.id = us.setting_id'
            );
            $qb->where('(us.user_id = ? OR us.user_id IS NULL)');
            $search_params[] = $user_id;
        } else {
            $qb->select('alias', 'setting_value');
            $qb->from('settings');
        }

        $settings = $this->db->fetchAll($qb->getSql(), $search_params);

        return $settings;
    }


    /**
     * Get setting by alias.
     */
    public function getSettingByAlias($alias, $user_id = 0) {
        $sql = NULL;
        $params = [$alias];
        if ($user_id) {
            $sql = "SELECT settings.id AS id, alias, description, users_settings.setting_value AS setting_value FROM settings JOIN users_settings ON users_settings.setting_id = settings.id WHERE alias = ? AND user_id = ?";
            $params[] = $user_id;
        } else {
            $sql = "SELECT id, alias, description, setting_value FROM settings WHERE alias = ?";
        }
        

        $query = $this->db->executeQuery($sql, $params);
        $entity = $query->fetch();

        return $entity;
    }


    /**
     * Delete user settings.
     */
    public function deleteUserSettings($user_id) {
        $this->db->delete('users_settings', ['user_id' => $user_id]);
    }


    /**
     * Save user setting
     */
    public function saveUserSetting($setting) {
        $this->db->insert('users_settings', $setting);
        return $this->db->lastInsertId();
    }

    /**
     * Update user setting.
     */
    public function updateUserSetting($setting_id, $user_id, $setting_value) {
        return $this->db->update('users_settings', ['setting_value' => $setting_value], ['setting_id' => $setting_id, 'user_id' => $user_id]);
    }

}
