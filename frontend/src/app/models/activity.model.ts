/**
 * =============================================================================
 * Activity & Live Sessions Models
 * =============================================================================
 */

export interface ActivityPlayer {
  name: string;
  role: string;
  skill: string;
}

export interface ActivityItem {
  id: number;
  title: string;
  groupName: string;
  groupSlug: string;
  venue: string;
  courtNumbers: string;
  date: string;
  time: string;
  skillLevel: string;
  priceEst: string;
  maxPlayers: number;
  confirmedPlayers: number;
  isJoined: boolean;
  players: ActivityPlayer[];
  facilities: string[];
}
