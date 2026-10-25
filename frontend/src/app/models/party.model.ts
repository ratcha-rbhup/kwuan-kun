/**
 * =============================================================================
 * Party & Group Discovery Card Models
 * =============================================================================
 */

export interface TargetSkill {
  label: string;
  cssClass: string;
}

export interface PartyCardItem {
  id: number;
  slug: string;
  type: 'KWUAN' | 'FIND_PLAYERS';
  typeLabel: string;
  title: string;
  subtitle: string;
  province: string;
  district: string;
  venueName: string;
  venueAddress: string;
  lat: number;
  lng: number;
  distanceKm: number;
  bannerImage: string;
  isoDate: string; // YYYY-MM-DD
  dateBadge: string;
  dateTimeDisplay: string;
  targetSkills: TargetSkill[];
  hostName: string;
  hostAvatar: string;
  hostRole: string;
  currentPlayers: number;
  maxPlayers: number;
  isFull: boolean;
  status: 'OPEN' | 'FULL';
}
