export interface SportCategory {
  id: number;
  code: string;
  name_th: string;
  name_en: string;
  icon?: string;
  description?: string;
  is_active: boolean;
  display_order: number;
}

export interface BadmintonSkillLevel {
  id: number;
  sport_category_id: number;
  code: string;
  name_th: string;
  name_en: string;
  level_score: number;
  description?: string;
  is_active: boolean;
  display_order: number;
}
