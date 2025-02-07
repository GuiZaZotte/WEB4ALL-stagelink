CREATE DATABASE StageManagement;
USE StageManagement;

-- Table des utilisateurs
CREATE TABLE Utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(100) NOT NULL,
    role ENUM('Administrateur', 'Pilote', 'Etudiant') NOT NULL
);

-- Table des entreprises
CREATE TABLE Entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL
);

-- Table des évaluations des entreprises
CREATE TABLE Evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    FOREIGN KEY (entreprise_id) REFERENCES Entreprises(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE
);

-- Table des offres de stage
CREATE TABLE Offres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    base_remuneration DECIMAL(10,2),
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (entreprise_id) REFERENCES Entreprises(id) ON DELETE CASCADE
);

-- Table des compétences
CREATE TABLE Competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL
);

-- Relation entre Offres et Compétences
CREATE TABLE Offres_Competences (
    offre_id INT NOT NULL,
    competence_id INT NOT NULL,
    PRIMARY KEY (offre_id, competence_id),
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES Competences(id) ON DELETE CASCADE
);

-- Table des candidatures
CREATE TABLE Candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    offre_id INT NOT NULL,
    date_candidature DATE NOT NULL,
    cv VARCHAR(100) NOT NULL,
    lettre_motivation TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE
);

-- Table de la wish-list des offres
CREATE TABLE WishList (
    utilisateur_id INT NOT NULL,
    offre_id INT NOT NULL,
    PRIMARY KEY (utilisateur_id, offre_id),
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE
);
