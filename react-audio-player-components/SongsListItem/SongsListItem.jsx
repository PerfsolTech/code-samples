import React from 'react'
import styled from 'styled-components'
import { useSelector } from 'react-redux'

const StyledListItem = styled.li``
const StyledListHeading = styled.h3``
const StyledListText = styled.p``
const StyledListButton = styled.button`
  border: none;
  background: transparent;
  width: 60px;
  height: 60px;
`
const StyledListPlayerImg = styled.img`
  display: block;
  width: 60px;
  height: 60px;
`

export default function SongsListItem({ song }) {
  const local = useSelector((state) => state.language.local)

  return (
    <StyledListItem>
      <StyledListHeading>{song[`name_${local}`]}</StyledListHeading>
      <StyledListText>{song[`text_${local}`]}</StyledListText>
      <StyledListButton>
        <StyledListPlayerImg src="../../img/png/player.png" />
      </StyledListButton>
    </StyledListItem>
  )
}
