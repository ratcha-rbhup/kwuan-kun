/**
 * =============================================================================
 * Kwuan Owner, Members, Sessions & Payment Models
 * =============================================================================
 */

export interface GroupMember {
  id: number;
  name: string;
  lineId: string;
  avatarUrl?: string;
  skillLevel: 'N' | 'N+' | 'S' | 'S+' | 'P' | 'P+' | 'C';
  role: 'HOST' | 'CO_HOST' | 'MEMBER';
  joinDate: string;
  sessionsCount: number;
  status: 'ACTIVE' | 'INACTIVE';
}

export interface PendingMemberRequest {
  id: number;
  name: string;
  lineId: string;
  skillLevel: string;
  appliedDate: string;
  message: string;
}

export interface PlayerPayment {
  id: number;
  name: string;
  gamesPlayed: number;
  shuttlesUsed: number;
  customFee?: number;
  isPaid: boolean;
  paidAt?: string;
}

export interface SessionPlayer {
  name: string;
  level: string;
  isPaid: boolean;
}

export interface BadmintonSession {
  id: number;
  title: string;
  date: string;
  time: string;
  courtVenue: string;
  courtNumbers: string;
  shuttlecockBrand: string;
  maxPlayers: number;
  registeredCount: number;
  priceEstimate: number;
  status: 'OPEN' | 'FULL' | 'COMPLETED' | 'CANCELLED';
  players: SessionPlayer[];
}
